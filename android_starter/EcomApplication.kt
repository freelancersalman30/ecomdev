package com.dreamerspcb.ecommerce

import android.app.Application
import dagger.hilt.android.HiltAndroidApp

@HiltAndroidApp
class EcomApplication : Application() {
    override fun onCreate() {
        super.onCreate()
    }
}
