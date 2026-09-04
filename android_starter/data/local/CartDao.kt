package com.dreamerspcb.ecommerce.data.local

import androidx.room.*
import kotlinx.coroutines.flow.Flow

@Entity(tableName = "cart_items")
data class CartEntity(
    @PrimaryKey(autoGenerate = true) val id: Long = 0,
    val productId: Long,
    val variantId: Long? = null,
    val name: String,
    val variantName: String? = null,
    val sku: String? = null,
    val thumbnail: String? = null,
    val unitPrice: Double,
    val quantity: Int
)

@Dao
interface CartDao {
    @Query("SELECT * FROM cart_items")
    fun getAllCartItems(): Flow<List<CartEntity>>

    @Insert(onConflict = OnConflictStrategy.REPLACE)
    suspend fun insertItem(item: CartEntity)

    @Update
    suspend fun updateItem(item: CartEntity)

    @Delete
    suspend fun deleteItem(item: CartEntity)

    @Query("DELETE FROM cart_items")
    suspend fun clearCart()

    @Query("SELECT SUM(quantity) FROM cart_items")
    fun getTotalCartCount(): Flow<Int?>
}

@Database(entities = [CartEntity::class], version = 1, exportSchema = false)
abstract class AppDatabase : RoomDatabase() {
    abstract fun cartDao(): CartDao
}
