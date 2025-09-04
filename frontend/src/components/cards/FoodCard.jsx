"use client"

import { useRouter } from "next/navigation"
import { useState } from "react"
import {AddToCartButton} from "@/components/buttons/buttons"
import styles from "./FoodCard.module.css"

export default function FoodCard({ food }) {
  const router = useRouter()
  const [imageError, setImageError] = useState(false)
  const imageUrl = food.image_url
    ? `${process.env.NEXT_PUBLIC_BASE_URL}${food.image_url}`
    : "/placeholder.svg?height=200&width=200&query=food";

  const handleCardClick = (e) => {
    // Don't navigate if clicking on the button
    if (e.target.closest("button")) {
      return
    }
    router.push(`/foods/${food.id}`)
  }

  const formatPrice = (price) => {
    return `$${Number.parseFloat(price).toFixed(2)}`
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
        {!food.is_available && <div className={styles.unavailableBadge}>Unavailable</div>}
      </div>

      <div className={styles.content}>
        <h3 className={styles.name}>{food.name}</h3>
        <p className={styles.description}>{food.description}</p>

        <div className={styles.details}>
          <div className={styles.price}>{formatPrice(food.price)}</div>
          <div className={styles.stock}>Stock: {food.stock}</div>
        </div>

        <div className={styles.actions}>
          <AddToCartButton foodId={food.id} isAvailable={food.is_available} disabled={!food.is_available} />
        </div>
      </div>
    </div>
  )
}
