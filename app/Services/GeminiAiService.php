<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Http;
use Throwable;

class GeminiAiService
{
    /**
     * Generate product description using Google Gemini API or Smart Fallback Engine.
     *
     * @return array{success: bool, description: string, short_description: string, source: string}
     */
    public function generateProductDescription(string $title, ?string $category = null, ?string $chipset = null): array
    {
        $cleanTitle = trim($title);
        if (empty($cleanTitle)) {
            return [
                'success' => false,
                'description' => '',
                'short_description' => '',
                'source' => 'error',
                'message' => 'Product name/title is required to generate a description.',
            ];
        }

        $apiKey = $this->getApiKey();

        if (! empty($apiKey)) {
            $apiResult = $this->callGeminiApi($cleanTitle, $category, $chipset, $apiKey);
            if ($apiResult['success']) {
                return $apiResult;
            }
        }

        // Use Built-in Smart Component Heuristics Generator
        return $this->generateHeuristicDescription($cleanTitle, $category, $chipset);
    }

    /**
     * Retrieve Gemini API Key from config, .env, or settings table.
     */
    public function getApiKey(): ?string
    {
        return config('services.gemini.api_key')
            ?: env('GEMINI_API_KEY')
            ?: Setting::get('gemini_api_key');
    }

    /**
     * Call Google Gemini 1.5 Flash REST API.
     */
    protected function callGeminiApi(string $title, ?string $category, ?string $chipset, string $apiKey): array
    {
        try {
            $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key={$apiKey}";

            $prompt = <<<PROMPT
You are a professional hardware engineer and technical copywriter for "DREAMERS PCB", an electronic components, robotics, and PCB engineering eCommerce store.
Generate an accurate, technical, and high-converting product description for: "{$title}".
Additional details: Category: {$category}, Chipset/Core: {$chipset}.

Your response MUST follow this exact clean Markdown structure:

## Product Overview
[Provide 1-2 clear, informative paragraphs explaining what this component does, its architecture, applications in DIY/robotics/industrial automation, and key advantages.]

### Key Hardware Features
- **[Feature Name]:** [Specification detail]
- **[Feature Name]:** [Specification detail]
- **[Feature Name]:** [Specification detail]
- **[Feature Name]:** [Specification detail]
- **[Feature Name]:** [Specification detail]

### Technical Specifications
| Parameter | Specification | Details / Conditions |
| :--- | :--- | :--- |
| **Operating Voltage** | [e.g. 3.3V ~ 5.0V DC] | [Tolerant range] |
| **Operating Current** | [e.g. 15mA (Idle) / 80mA (Peak)] | [Typical] |
| **Main Chip / IC** | [IC model or discrete type] | [Package type] |
| **Interface / Protocols** | [SPI / I2C / UART / GPIO / Analog] | [Bus speed] |
| **Operating Temperature** | -40°C to +85°C | Industrial Grade |
| **Dimensions & Pitch** | [Standard size, e.g. 2.54mm pin pitch] | Breadboard Friendly |

### Pinout & Peripheral Guide
- **VCC:** Power Supply Positive Input
- **GND:** Ground Reference Line
- [List 2-4 primary signal/data pins and their hardware functions]

> **Engineering Tip:** [Provide 1 realistic engineering/PCB tip regarding bypass capacitors, heat dissipation, voltage levels, or solder joint advice.]

Do NOT include pleasantries or conversational filler. Output only clean Markdown.
PROMPT;

            $response = Http::timeout(15)->post($url, [
                'contents' => [
                    [
                        'parts' => [
                            ['text' => $prompt],
                        ],
                    ],
                ],
                'generationConfig' => [
                    'temperature' => 0.3,
                    'maxOutputTokens' => 1200,
                ],
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $generatedMarkdown = $data['candidates'][0]['content']['parts'][0]['text'] ?? null;

                if (! empty($generatedMarkdown)) {
                    $shortDescription = $this->extractShortDescription($generatedMarkdown, $title);

                    return [
                        'success' => true,
                        'description' => trim($generatedMarkdown),
                        'short_description' => $shortDescription,
                        'source' => 'gemini_api',
                    ];
                }
            }
        } catch (Throwable $e) {
            report($e);
        }

        return ['success' => false];
    }

    /**
     * Smart Electronic Component Heuristic AI Engine.
     * Generates accurate technical specs based on product keyword analysis.
     */
    public function generateHeuristicDescription(string $title, ?string $category = null, ?string $chipset = null): array
    {
        $lower = strtolower($title);

        $type = 'generic';
        $voltage = '3.3V ~ 5.0V DC';
        $current = '20mA ~ 100mA';
        $core = $chipset ?: 'Integrated Silicon IC';
        $interfaces = 'GPIO / Analog / Digital';
        $pins = [
            'VCC' => 'Primary DC Power Input',
            'GND' => 'System Common Ground',
            'SIG / DATA' => 'Digital / Analog Signal I/O',
        ];
        $tip = 'Place a 100nF decoupling capacitor across power input pins for optimal RF and transient noise suppression.';

        // Detect Microcontrollers
        if (str_contains($lower, 'esp32') || str_contains($lower, 'nodemcu-32')) {
            $type = 'esp32';
            $voltage = '3.0V ~ 3.6V DC (5V Tolerant via USB/VBUS)';
            $current = '80mA (Active) / 240mA (RF Peak) / 5µA (Deep Sleep)';
            $core = 'Xtensa Dual-Core 32-bit LX6 Microprocessor @ 240 MHz';
            $interfaces = 'Wi-Fi 802.11 b/g/n, Bluetooth v4.2 BR/EDR & BLE, SPI, I2C, UART, PWM, ADC, DAC';
            $pins = [
                'VIN / 5V' => 'Regulated 5V DC Power Input',
                '3V3' => 'Regulated 3.3V Output (Up to 600mA)',
                'GND' => 'Common Ground Reference',
                'TX0 / RX0' => 'Hardware Serial Programming UART',
                'GPIO21 / GPIO22' => 'Default Hardware I2C (SDA / SCL)',
                'EN' => 'Active-High Chip Enable / Reset Pin',
            ];
            $tip = 'When transmitting high-power Wi-Fi packets, ensure power supply can deliver at least 500mA transient current to prevent brownout resets.';
        } elseif (str_contains($lower, 'esp8266') || str_contains($lower, 'nodemcu') || str_contains($lower, 'd1 mini')) {
            $type = 'esp8266';
            $voltage = '3.0V ~ 3.6V (5V via USB)';
            $current = '70mA (Average) / 170mA (TX Peak)';
            $core = 'Tensilica L106 32-bit RISC Core @ 80/160 MHz';
            $interfaces = 'Wi-Fi 802.11 b/g/n (2.4 GHz), I2C, SPI, UART, 10-bit ADC';
            $pins = [
                'VIN' => '5V External Power Input',
                '3V3' => 'Regulated 3.3V Output',
                'GND' => 'System Ground',
                'D1 / D2' => 'Standard I2C (SCL / SDA)',
                'A0' => 'Analog Input (Max 3.3V via divider)',
            ];
            $tip = 'Keep GPIO0 and GPIO2 pulled high for normal SPI Flash boot execution.';
        } elseif (str_contains($lower, 'stm32') || str_contains($lower, 'blue pill') || str_contains($lower, 'cortex')) {
            $type = 'stm32';
            $voltage = '2.0V ~ 3.6V DC (5V tolerant GPIOs on select pins)';
            $current = '35mA ~ 50mA';
            $core = 'ARM 32-bit Cortex-M3 CPU @ 72 MHz';
            $interfaces = 'CAN, USB 2.0 Full-Speed, SPI (x2), I2C (x2), USART (x3), 12-bit ADC';
            $pins = [
                '3.3V' => 'Logic Level Power Supply',
                'GND' => 'Ground Reference',
                'SWDIO / SWCLK' => 'Serial Wire Debug (SWD) Programming Header',
                'PA9 / PA10' => 'USART1 Serial Transmit / Receive',
                'PB6 / PB7' => 'Hardware I2C1 Bus',
            ];
            $tip = 'Ensure boot jumpers BOOT0 and BOOT1 are set to position 0 for standard user Flash execution.';
        } elseif (str_contains($lower, 'arduino') || str_contains($lower, 'atmega328') || str_contains($lower, 'nano')) {
            $type = 'arduino';
            $voltage = '5V DC (7V - 12V via VIN/Raw)';
            $current = '20mA ~ 45mA';
            $core = 'Microchip ATmega328P 8-bit AVR Microcontroller @ 16 MHz';
            $interfaces = 'UART, SPI, I2C, 6x PWM, 8x 10-bit Analog Inputs';
            $pins = [
                'VIN' => 'Unregulated 7-12V Power Input',
                '5V' => 'Regulated 5V System Power',
                'GND' => 'Common Ground',
                'D0 / D1' => 'Hardware UART RX / TX',
                'A4 / A5' => 'Hardware I2C SDA / SCL',
            ];
            $tip = 'Do not draw more than 40mA per individual I/O pin to protect internal silicon output drivers.';
        } elseif (str_contains($lower, 'step down') || str_contains($lower, 'buck') || str_contains($lower, 'lm2596')) {
            $type = 'buck';
            $voltage = 'Input: 4.5V - 40V DC | Output: 1.25V - 35V Adjustable';
            $current = 'Up to 3A (2A continuous without extra heatsink)';
            $core = 'Monolithic Switch-Mode Switching Regulator';
            $interfaces = 'Screw Terminals & PCB Solder Pads';
            $pins = [
                'IN+ / IN-' => 'DC Voltage Input',
                'OUT+ / OUT-' => 'Regulated DC Output',
            ];
            $tip = 'Input voltage must be at least 1.5V higher than the desired output voltage for stable switch-mode regulation.';
        } elseif (str_contains($lower, 'boost') || str_contains($lower, 'step up') || str_contains($lower, 'xl6009') || str_contains($lower, 'mt3608')) {
            $type = 'boost';
            $voltage = 'Input: 3V - 32V DC | Output: 5V - 35V Adjustable';
            $current = 'Up to 4A Peak Switching Current';
            $core = 'High-frequency Boost DC-DC Step-Up Controller';
            $interfaces = 'Heavy-Duty Solder Terminals';
            $pins = [
                'VIN+ / VIN-' => 'Low-voltage DC Input',
                'VOUT+ / VOUT-' => 'High-voltage Boosted DC Output',
            ];
            $tip = 'Never run boost converters without load at maximum trim pot setting to prevent output capacitor overvoltage.';
        } elseif (str_contains($lower, 'sensor') || str_contains($lower, 'dht') || str_contains($lower, 'bme') || str_contains($lower, 'bmp') || str_contains($lower, 'mpu')) {
            $type = 'sensor';
            $voltage = '3.3V ~ 5.0V DC';
            $current = '1.5mA (Measurement) / 10µA (Standby)';
            $core = 'Precision Calibrated MEMS Sensor IC';
            $interfaces = 'Standard I2C (Standard & Fast Mode) / SPI / Digital 1-Wire';
            $pins = [
                'VCC' => 'Power Supply (3.3V / 5V)',
                'GND' => 'Ground Reference',
                'SDA / SCL' => 'I2C Serial Data and Clock',
                'INT' => 'Programmable Interrupt Pin',
            ];
            $tip = 'Keep I2C pull-up resistors (4.7kΩ) short and close to master to prevent bus capacitance noise.';
        } elseif (str_contains($lower, 'relay')) {
            $type = 'relay';
            $voltage = 'Control: 5V DC | Switching: AC 250V/10A, DC 30V/10A';
            $current = '65mA ~ 80mA per coil';
            $core = 'Electromechanical Relay with Optocoupler Isolation';
            $interfaces = 'Active-Low / Active-High Trigger Input';
            $pins = [
                'VCC / GND' => 'Relay Logic Power',
                'IN' => 'Digital Trigger Signal',
                'COM' => 'Common Switch Terminal',
                'NO / NC' => 'Normally Open / Normally Closed Contact',
            ];
            $tip = 'Always isolate AC mains high-voltage copper traces on PCB with milled isolation slots.';
        }

        $pinRows = '';
        foreach ($pins as $pin => $desc) {
            $pinRows .= "- **{$pin}:** {$desc}\n";
        }

        $shortDescription = "The {$title} is a high-grade electronic component engineered for reliable performance, seamless prototyping, and industrial PCB integration.";

        $markdown = <<<MARKDOWN
## Product Overview
The **{$title}** is an industrial-grade high-reliability electronic module engineered for IoT developers, embedded systems engineers, and precision electronics prototyping. Built with high-spec components and clean PCB layout, it ensures stable performance across demanding operating environments.

Ideal for educational robotics, industrial automation, DIY microelectronics, and custom embedded product development.

### Key Hardware Features
- **Robust Architecture:** Powered by {$core} for responsive and reliable processing.
- **Flexible Power Input:** Wide operating range of {$voltage} with transient noise tolerance.
- **Standard Connectivity:** Supports {$interfaces} for effortless integration with microcontrollers and sensors.
- **Compact Breadboard Layout:** Standard 2.54mm pin spacing enables solderless breadboard testing and custom PCB mounting.
- **Thermal & Circuit Reliability:** Engineered with thermal relief copper planes and durable surface-mount passive components.

### Technical Specifications
| Parameter | Specification | Details / Notes |
| :--- | :--- | :--- |
| **Operating Voltage** | {$voltage} | Stable DC input |
| **Operating Current** | {$current} | Energy efficient |
| **Silicon Core / Controller** | {$core} | High performance |
| **Supported Interfaces** | {$interfaces} | Flexible I/O |
| **Operating Temperature** | -40°C to +85°C | Industrial Grade |
| **Mounting / Pin Pitch** | 2.54mm (0.1") | Breadboard compatible |

### Pinout & Peripheral Guide
{$pinRows}
> **Engineering Tip:** {$tip}
MARKDOWN;

        return [
            'success' => true,
            'description' => trim($markdown),
            'short_description' => $shortDescription,
            'source' => 'component_engine',
        ];
    }

    /**
     * Extract a punchy short description from the generated markdown text.
     */
    protected function extractShortDescription(string $markdown, string $fallbackTitle): string
    {
        if (preg_match('/## Product Overview\s+([^\n\r]+)/i', $markdown, $matches)) {
            $sentence = trim(strip_tags($matches[1]));
            $sentence = preg_replace('/[*_#`]/', '', $sentence);
            if (strlen($sentence) > 240) {
                $sentence = substr($sentence, 0, 237).'...';
            }

            return $sentence;
        }

        return "High-performance {$fallbackTitle} engineered for reliable embedded systems, robotics, and precision PCB prototyping.";
    }
}
