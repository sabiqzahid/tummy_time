"use client"

import { useState } from "react"
import styles from "./MostSoldFoodCard.module.css"

export default function MostSoldFoodCard({ food, rank, onClick }) {
  const [imageError, setImageError] = useState(false)
  const imageUrl = food.image_url
    ? `${process.env.NEXT_PUBLIC_BASE_URL}${food.image_url}`
    : "/placeholder.svg?height=200&width=200&query=food";

  const handleCardClick = () => {
    onClick()
  }

  const getRankBadgeClass = (rank) => {
    if (rank === 1) return styles.gold
    if (rank === 2) return styles.silver
    if (rank === 3) return styles.bronze
    return styles.default
  }

  return (
    <div className={styles.card} onClick={handleCardClick}>
      <div className={styles.rankBadge}>
        <span className={`${styles.rank} ${getRankBadgeClass(rank)}`}>#{rank}</span>
      </div>

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
      </div>

      <div className={styles.content}>
        <h3 className={styles.name}>{food.name}</h3>
        <p className={styles.description}>{food.description}</p>

        <div className={styles.stats}>
          <div className={styles.statItem}>
            <span className={styles.statLabel}>Price:</span>
            <span className={styles.price}>${Number.parseFloat(food.price).toFixed(2)}</span>
          </div>

          <div className={styles.statItem}>
            <span className={styles.statLabel}>Stock:</span>
            <span className={styles.stock}>{food.stock}</span>
          </div>

          <div className={styles.statItem}>
            <span className={styles.statLabel}>Sold:</span>
            <span className={styles.sold}>{food.sold}</span>
          </div>
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

