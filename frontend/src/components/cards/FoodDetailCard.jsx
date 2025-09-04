"use client"

import { useState } from "react"
import {AddToCartButton} from "@/components/buttons/buttons"
import styles from "./FoodDetailCard.module.css"

export default function FoodDetailCard({ food, onUpdate, onDelete, isAdmin = false }) {
  const [imageError, setImageError] = useState(false)
  const imageUrl = food.image_url
    ? `${process.env.NEXT_PUBLIC_BASE_URL}${food.image_url}`
    : "/placeholder.svg?height=200&width=200&query=food";

  const handleImageError = () => {
    setImageError(true)
  }

  return (
    <div className={styles.card}>
      <div className={styles.imageContainer}>
        {!imageError ? (
          <img
            src={imageUrl}
            alt={food.name}
            className={styles.image}
            onError={handleImageError}
          />
        ) : (
          <div className={styles.imagePlaceholder}>
            <span>No Image</span>
          </div>
        )}
      </div>

      <div className={styles.content}>
        <div className={styles.header}>
          <h2 className={styles.name}>{food.name}</h2>
          <div className={styles.price}>${food.price}</div>
        </div>

        <p className={styles.description}>{food.description}</p>

        <div className={styles.details}>
          <div className={styles.detailItem}>
            <span className={styles.label}>Stock:</span>
            <span className={styles.value}>{food.stock}</span>
          </div>

          <div className={styles.detailItem}>
            <span className={styles.label}>Sold:</span>
            <span className={styles.value}>{food.sold}</span>
          </div>

          <div className={styles.detailItem}>
            <span className={styles.label}>Available:</span>
            <span className={`${styles.value} ${food.is_available ? styles.available : styles.unavailable}`}>
              {food.is_available ? "Yes" : "No"}
            </span>
          </div>

          <div className={styles.detailItem}>
            <span className={styles.label}>Category: </span>
            <span className={styles.value}>{food.category.name}</span>
          </div>
        </div>

        <div className={styles.actions}>
          {isAdmin ? (
            <>
              <button onClick={onUpdate} className={styles.updateButton}>
                Update Food
              </button>
              <button onClick={onDelete} className={styles.deleteButton}>
                Delete Food
              </button>
            </>
          ) : (
            <AddToCartButton foodId={food.id} isAvailable={food.is_available} stock={food.stock} />
          )}
        </div>
      </div>
    </div>
  )
}

