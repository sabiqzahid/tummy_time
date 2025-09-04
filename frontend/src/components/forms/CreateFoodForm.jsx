"use client";
import { useFormStatus } from "react-dom";
import { useState } from "react";
import { SubmitButton } from "@/components/buttons/buttons";
import styles from "./CreateFoodForm.module.css";

function FormContent({ categories, errors, successMessage }) {
  const { pending } = useFormStatus();
  const [fileName, setFileName] = useState("No file chosen");

  const handleFileChange = (event) => {
    const file = event.target.files[0];
    if (file) {
      setFileName(file.name);
    } else {
      setFileName("No file chosen");
    }
  }

  return (
    <>
      {successMessage && <div className={styles.success}>{successMessage}</div>}

      <div className={styles.formGroup}>
        <label htmlFor="name" className={styles.label}>
          Food Name *
        </label>
        <input
          type="text"
          id="name"
          name="name"
          className={`${styles.input} ${errors.name ? styles.inputError : ""}`}
          placeholder="Enter food name"
          required
          disabled={pending}
        />
        {errors.name && <span className={styles.errorText}>{errors.name}</span>}
      </div>

      <div className={styles.formGroup}>
        <label htmlFor="category" className={styles.label}>
          Category *
        </label>
        <select
          id="category"
          name="category_id"
          className={`${styles.select} ${
            errors.category ? styles.inputError : ""
          }`}
          required
          disabled={pending}
        >
          <option value="">Select a category</option>
          {categories.map((category) => (
            <option key={category.id} value={category.id}>
              {category.name}
            </option>
          ))}
        </select>
        {errors.category && (
          <span className={styles.errorText}>{errors.category}</span>
        )}
      </div>

      <div className={styles.formGroup}>
        <label htmlFor="description" className={styles.label}>
          Description *
        </label>
        <textarea
          id="description"
          name="description"
          className={`${styles.textarea} ${
            errors.description ? styles.inputError : ""
          }`}
          placeholder="Enter food description"
          rows={4}
          required
          disabled={pending}
        />
        {errors.description && (
          <span className={styles.errorText}>{errors.description}</span>
        )}
      </div>

      <div className={styles.formRow}>
        <div className={styles.formGroup}>
          <label htmlFor="price" className={styles.label}>
            Price *
          </label>
          <input
            type="number"
            id="price"
            name="price"
            step="0.01"
            min="0"
            className={`${styles.input} ${
              errors.price ? styles.inputError : ""
            }`}
            placeholder="0.00"
            required
            disabled={pending}
          />
          {errors.price && (
            <span className={styles.errorText}>{errors.price}</span>
          )}
        </div>

        <div className={styles.formGroup}>
          <label htmlFor="stock" className={styles.label}>
            Stock *
          </label>
          <input
            type="number"
            id="stock"
            name="stock"
            min="0"
            className={`${styles.input} ${
              errors.stock ? styles.inputError : ""
            }`}
            placeholder="0"
            required
            disabled={pending}
          />
          {errors.stock && (
            <span className={styles.errorText}>{errors.stock}</span>
          )}
        </div>
      </div>

      <div className={styles.formGroup}>
        <div className={styles.Foodinputgroup}>
          <label htmlFor="image" className={styles.Foodlabel}>Food Image</label>
          

          <input
              type="file"
              id="image"
              name="image_url"
              accept="image/*"
              onChange={handleFileChange}
              className={styles.Foodhidden}
          />

          <div className={styles.Foodfileupload}>
              <label htmlFor="image" className={styles.Foodbutton}>
                  Choose File
              </label>
              
              <span id="fileNameDisplay" className={styles.Foodfilename}>{fileName}</span>
          </div>
        </div>
        {errors.image && <span className={styles.errorText}>{errors.image}</span>}
      </div>

      {errors.error && <div className={styles.error}>{errors.error}</div>}

      <div className={styles.actions}>
        <SubmitButton loading={pending}>Create Food</SubmitButton>
      </div>
    </>
  );
}

export default function CreateFoodForm({
  onSubmit,
  categories,
  loading,
  errors,
  successMessage,
}) {
  const handleSubmit = async (formData) => {
    await onSubmit(formData);
  };

  return (
    <form action={handleSubmit} className={styles.form}>
      <FormContent
        categories={categories}
        errors={errors}
        successMessage={successMessage}
      />
    </form>
  );
}
