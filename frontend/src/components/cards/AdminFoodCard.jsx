"use client"

import { useState } from "react"
import {RemoveButton} from "@/components/buttons/buttons"
import styles from "./AdminFoodCard.module.css"

export default function AdminFoodCard({ food, onDelete, onClick }) {
  const [imageError, setImageError] = useState(false)
  const imageUrl = food.image_url
    ? `${process.env.NEXT_PUBLIC_BASE_URL}${food.image_url}`
    : "/placeholder.svg?height=200&width=200&query=food";

  console.log(food.image_url)

  const handleCardClick = (e) => {
    // Don't trigger card click if delete button was clicked
    if (e.target.closest("button")) {
      return
    }
    onClick()
  }

  const handleDeleteClick = (e) => {
    e.stopPropagation()
    onDelete()
  }

  return (
    <div className={styles.card} onClick={handleCardClick}>
      <div className={styles.imageContainer}>
        {!imageError ? (
          <img
            src={imageUrl}
            alt={food.name}
            className={styles.image}
            onError={() => setImageError(true)}
          />
        ) : (
          <div className={styles.imagePlaceholder}>
            <span>No Image</span>
          </div>
        )}
        <div className={styles.deleteButton}>
          <RemoveButton onClick={handleDeleteClick} />
        </div>
      </div>

      <div className={styles.content}>
        <h3 className={styles.name}>{food.name}</h3>
        <p className={styles.description}>{food.description}</p>

        <div className={styles.details}>
          <div className={styles.price}>${Number.parseFloat(food.price).toFixed(2)}</div>
          <div className={styles.stock}>Stock: {food.stock}</div>
        </div>

        <div className={styles.meta}>
          <span className={styles.id}>ID: {food.id}</span>
          <span className={`${styles.availability} ${food.is_available ? styles.available : styles.unavailable}`}>
            {food.is_available ? "Available" : "Unavailable"}
          </span>
        </div>
      </div>
    </div>
  )
}

