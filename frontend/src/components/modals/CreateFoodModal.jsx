"use client"

import { useState } from "react"
import { createFoodAction } from "@/actions/foodActions"
import CreateFoodForm from "@/components/forms/CreateFoodForm"
import styles from "./CreateFoodModal.module.css"

export default function CreateFoodModal({ isOpen, onClose, categories, onSuccess }) {
  const [loading, setLoading] = useState(false)
  const [errors, setErrors] = useState({})
  const [successMessage, setSuccessMessage] = useState("")

  const handleSubmit = async (formData) => {
    setLoading(true);
    setErrors({});
    setSuccessMessage("");

    try {
      const result = await createFoodAction(formData)

      if (result.error) {
        setErrors(result.error)
      } else if (result.success) {
        setSuccessMessage("Food created successfully!")
        setTimeout(() => {
          onSuccess()
        }, 1500)
      }
    } catch (err) {
      setErrors({ error: "Failed to create food" })
    } finally {
      setLoading(false)
    }
  }

  if (!isOpen) return null

  return (
    <div className={styles.overlay}>
      <div className={styles.modal}>
        <div className={styles.header}>
          <h2 className={styles.title}>Create New Food</h2>
          <button onClick={onClose} className={styles.closeButton}>
            ×
          </button>
        </div>

        <div className={styles.content}>
          <CreateFoodForm
            onSubmit={handleSubmit}
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

