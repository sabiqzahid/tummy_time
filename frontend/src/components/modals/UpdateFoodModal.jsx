"use client"

import { useState } from "react"
import { updateFoodAction, getFoodAction } from "@/actions/foodActions"
import UpdateFoodForm from "@/components/forms/UpdateFoodForm"
import styles from "./UpdateFoodModal.module.css"

export default function UpdateFoodModal({ isOpen, onClose, food, categories, onSuccess }) {
  const [loading, setLoading] = useState(false)
  const [errors, setErrors] = useState({})
  const [successMessage, setSuccessMessage] = useState("")

  const handleSubmit = async (formData) => {
    setLoading(true)
    setErrors({})
    setSuccessMessage("")

    try {
      const result = await updateFoodAction(food.id, formData)

      if (result.error) {
        setErrors(result.error)
      } else if (result.success) {
        setSuccessMessage("Food updated successfully!")
        // Fetch updated food data
        const updatedFoodResponse = await getFoodAction(food.id)
        if (updatedFoodResponse.data) {
          setTimeout(() => {
            onSuccess(updatedFoodResponse.data)
          }, 1500)
        }
      }
    } catch (err) {
      setErrors({ error: "Failed to update food" })
    } finally {
      setLoading(false)
    }
  }

  if (!isOpen) return null

  return (
    <div className={styles.overlay}>
      <div className={styles.modal}>
        <div className={styles.header}>
          <h2 className={styles.title}>Update Food</h2>
          <button onClick={onClose} className={styles.closeButton}>
            ×
          </button>
        </div>

        <div className={styles.content}>
          <UpdateFoodForm
            onSubmit={handleSubmit}
            food={food}
            categories={categories}
            loading={loading}
            errors={errors}
            successMessage={successMessage}
          />
        </div>
      </div>
    </div>
  )
}

