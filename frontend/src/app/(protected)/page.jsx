"use client"

import { useState, useEffect } from "react"
import { getFoodsAction } from "@/actions/foodActions"
import { getCategoriesAction } from "@/actions/categoryActions"
import Image from "next/image"
import styles from "./page.module.css"

export default function HomePage() {
  const [foods, setFoods] = useState([])
  const [categories, setCategories] = useState([])
  const [showAllFoods, setShowAllFoods] = useState(false)
  const [showAllCategories, setShowAllCategories] = useState(false)
  const [loading, setLoading] = useState(true)

  useEffect(() => {
    const fetchData = async () => {
      try {
        const [foodsResponse, categoriesResponse] = await Promise.all([
          getFoodsAction({ per_page: 12 }),
          getCategoriesAction(),
        ])

        if (foodsResponse.data) {
          setFoods(foodsResponse.data)
        }
        if (categoriesResponse.data) {
          setCategories(categoriesResponse.data)
        }
      } catch (error) {
        console.error("Error fetching data:", error)
      } finally {
        setLoading(false)
      }
    }

    fetchData()
  }, [])

  const displayedFoods = showAllFoods ? foods : foods.slice(0, 6)
  const displayedCategories = showAllCategories ? categories : categories.slice(0, 8)

  if (loading) {
    return (
      <div className={styles.loadingContainer}>
        <div className={styles.spinner}></div>
      </div>
    )
  }

  return (
    <div className={styles.container}>
      {/* Hero Section */}
      <section className={styles.heroSection}>
        <div className={styles.heroBackground}>
          <Image
            src="/elegant-restaurant-interior-with-warm-lighting-and.png"
            alt="Tummy Time Restaurant"
            fill
            className={styles.heroImage}
            priority
          />
          <div className={styles.heroOverlay}></div>
        </div>

        <div className={styles.heroContent}>
          <h1 className={styles.heroTitle}>Tummy Time</h1>
          <p className={styles.heroSubtitle}>Where Every Bite is a Moment to Savor</p>
          <button className={styles.heroButton}>
            <a className={styles.foodLink} href="/foods">Explore Our Menu</a>
          </button>
        </div>
      </section>

      {/* Restaurant Story */}
      <section className={styles.storySection}>
        <div className={styles.storyContainer}>
          <div className={styles.storyGrid}>
            <div className={styles.storyContent}>
              <h2 className={styles.storyTitle}>Our Story</h2>
              <p className={styles.storyText}>
                At Tummy Time, we believe that dining is more than just eating—it's about creating moments that nourish
                both body and soul. Founded with a passion for exceptional cuisine and warm hospitality, we've crafted a
                space where every dish tells a story.
              </p>
              <p className={styles.storyText}>
                Our chefs combine traditional techniques with innovative flavors, sourcing the finest ingredients to
                create memorable experiences that bring people together around the table.
              </p>
              <div className={styles.storyFeatures}>
                <div className={styles.feature}>
                  <div className={styles.featureIcon}>
                    <span className={styles.icon}>⭐</span>
                  </div>
                  <p className={styles.featureText}>Premium Quality</p>
                </div>
                <div className={styles.feature}>
                  <div className={styles.featureIcon}>
                    <span className={styles.icon}>🕐</span>
                  </div>
                  <p className={styles.featureText}>Fresh Daily</p>
                </div>
                <div className={styles.feature}>
                  <div className={styles.featureIcon}>
                    <span className={styles.icon}>👨‍👩‍👧‍👦</span>
                  </div>
                  <p className={styles.featureText}>Family Owned</p>
                </div>
              </div>
            </div>
            <div className={styles.storyImageContainer}>
              <Image
                src="/warm-restaurant-kitchen-with-chef-preparing-gourme.png"
                alt="Our Kitchen"
                width={500}
                height={600}
                className={styles.storyImage}
              />
            </div>
          </div>
        </div>
      </section>

      {/* Categories Section */}
      <section className={styles.categoriesSection}>
        <div className={styles.categoriesContainer}>
          <div className={styles.sectionHeader}>
            <h2 className={styles.sectionTitle}>Our Categories</h2>
            <p className={styles.sectionSubtitle}>
              Discover our carefully curated selection of culinary categories, each offering unique flavors and
              experiences.
            </p>
          </div>

          <div className={styles.categoriesGrid}>
            {displayedCategories.map((category) => (
              <div key={category.id} className={styles.categoryCard}>
                <div className={styles.categoryContent}>
                  <div className={styles.categoryIcon}>
                    <span className={styles.categoryLetter}>{category.name.charAt(0).toUpperCase()}</span>
                  </div>
                  <h3 className={styles.categoryName}>{category.name}</h3>
                </div>
              </div>
            ))}
          </div>

          <div className={styles.sectionButton}>
            <button className={styles.showMoreButton} onClick={() => setShowAllCategories(!showAllCategories)}>
              {showAllCategories ? (
                <>
                  Show Less <span className={styles.buttonIcon}>⬆️</span>
                </>
              ) : (
                <>
                  Show More <span className={styles.buttonIcon}>⬇️</span>
                </>
              )}
            </button>
          </div>
        </div>
      </section>

      {/* Featured Foods Section */}
      <section className={styles.foodsSection}>
        <div className={styles.foodsContainer}>
          <div className={styles.sectionHeader}>
            <h2 className={styles.sectionTitle}>Featured Dishes</h2>
            <p className={styles.sectionSubtitle}>
              Indulge in our signature creations, crafted with passion and served with love.
            </p>
          </div>

          <div className={styles.foodsGrid}>
            {displayedFoods.map((food) => (
              <div key={food.id} className={styles.foodCard}>
                <div className={styles.foodImageContainer}>
                  <Image
                    src={
                      food.image_url
                      ? `${process.env.NEXT_PUBLIC_BASE_URL}${food.image_url}`
                      : "/placeholder.svg?height=200&width=200&query=food"
                    }
                    alt={food.name}
                    fill
                    className={styles.foodImage}
                  />
                  <div className={styles.foodPrice}>${food.price}</div>
                </div>
                <div className={styles.foodContent}>
                  <h3 className={styles.foodName}>{food.name}</h3>
                  <p className={styles.foodDescription}>{food.description}</p>
                  <div className={styles.foodStats}>
                    <span className={styles.foodStat}>Stock: {food.stock}</span>
                    <span className={styles.foodStat}>Sold: {food.sold}</span>
                  </div>
                </div>
              </div>
            ))}
          </div>

          <div className={styles.sectionButton}>
            <button className={styles.showMoreButton} onClick={() => setShowAllFoods(!showAllFoods)}>
              {showAllFoods ? (
                <>
                  Show Less <span className={styles.buttonIcon}>⬆️</span>
                </>
              ) : (
                <>
                  Show More <span className={styles.buttonIcon}>⬇️</span>
                </>
              )}
            </button>
          </div>
        </div>
      </section>

      {/* Call to Action */}
      <section className={styles.ctaSection}>
        <div className={styles.ctaContainer}>
          <h2 className={styles.ctaTitle}>Ready for Your Tummy Time?</h2>
          <p className={styles.ctaSubtitle}>
            Join us for an unforgettable dining experience where every meal is a celebration.
          </p>
          <div className={styles.ctaButtons}>
            <button className={styles.ctaPrimary}>
              <a className={styles.foodLink1} href="/foods">Order Now</a>
            </button>
            <button className={styles.ctaSecondary}>
              <a className={styles.foodLink2} href="/foods">View Full Menu</a>
            </button>
          </div>
        </div>
      </section>
    </div>
  )
}
