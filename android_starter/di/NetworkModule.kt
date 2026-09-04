package com.dreamerspcb.ecommerce.di

import android.content.Context
import androidx.room.Room
import com.dreamerspcb.ecommerce.data.api.ApiService
import com.dreamerspcb.ecommerce.data.local.AppDatabase
import com.dreamerspcb.ecommerce.data.local.CartDao
import dagger.Module
import dagger.Provides
import dagger.hilt.InstallIn
import dagger.hilt.android.qualifiers.ApplicationContext
import dagger.hilt.components.SingletonComponent
import okhttp3.Interceptor
import okhttp3.OkHttpClient
import okhttp3.logging.HttpLoggingInterceptor
import retrofit2.Retrofit
import retrofit2.converter.gson.GsonConverterFactory
import java.util.concurrent.TimeUnit
import javax.inject.Singleton

@Module
@InstallIn(SingletonComponent::class)
object NetworkModule {

    private const val BASE_URL = "http://10.0.2.2:8000/api/v1/" // 10.0.2.2 for Android Emulator -> Localhost

    @Provides
    @Singleton
    fun provideAuthInterceptor(@ApplicationContext context: Context): Interceptor {
        return Interceptor { chain ->
            val prefs = context.getSharedPreferences("dreamers_auth", Context.MODE_PRIVATE)
            val token = prefs.getString("auth_token", null)

            val requestBuilder = chain.request().newBuilder()
                .addHeader("Accept", "application/json")
                .addHeader("Content-Type", "application/json")

            if (!token.isNullOrEmpty()) {
                requestBuilder.addHeader("Authorization", "Bearer $token")
            }

            chain.proceed(requestBuilder.build())
        }
    }

    @Provides
    @Singleton
    fun provideOkHttpClient(authInterceptor: Interceptor): OkHttpClient {
        val logging = HttpLoggingInterceptor().apply {
            level = HttpLoggingInterceptor.Level.BODY
        }

        return OkHttpClient.Builder()
            .addInterceptor(authInterceptor)
            .addInterceptor(logging)
            .connectTimeout(30, TimeUnit.SECONDS)
            .readTimeout(30, TimeUnit.SECONDS)
            .writeTimeout(30, TimeUnit.SECONDS)
            .build()
    }

    @Provides
    @Singleton
    fun provideApiService(okHttpClient: OkHttpClient): ApiService {
        return Retrofit.Builder()
            .baseUrl(BASE_URL)
            .client(okHttpClient)
            .addConverterFactory(GsonConverterFactory.create())
            .build()
            .create(ApiService::class.java)
    }

    @Provides
    @Singleton
    fun provideAppDatabase(@ApplicationContext context: Context): AppDatabase {
        return Room.databaseBuilder(
            context,
            AppDatabase::class.java,
            "dreamers_ecommerce_db"
        ).fallbackToDestructiveMigration().build()
    }

    @Provides
    fun provideCartDao(database: AppDatabase): CartDao {
        return database.cartDao()
    }
}
