"use client"

import { useRouter } from "next/navigation"
import AdminDashboardCard from "@/components/cards/AdminDashboardCard"
import styles from "./page.module.css"

export default function AdminDashboard() {
  const router = useRouter()

  const dashboardOptions = [
    {
      id: 1,
      title: "Manage Foods",
      description: "View, edit and delete food items",
      icon: "🍽️",
      route: "/admin-dashboard/foods",
    },
    {
      id: 2,
      title: "Most Sold Foods",
      description: "View the most popular food items",
      icon: "📈",
      route: "/admin-dashboard/most-sold-foods",
    },
    {
      id: 3,
      title: "Manage Categories",
      description: "Create, edit and delete categories",
      icon: "📂",
      route: "/admin-dashboard/categories",
    },
    {
      id: 4,
      title: "Manage Orders",
      description: "View and manage customer orders",
      icon: "📋",
      route: "/admin-dashboard/orders",
    },
    {
      id: 4,
      title: "Create Staff",
      description: "Create new staff accounts",
      icon: "👥",
      route: "/admin-dashboard/staff",
    },
  ]

  const handleCardClick = (route) => {
    router.push(route)
  }

  return (
    <div className={styles.container}>
      <div className={styles.header}>
        <button onClick={() => router.back()} className={styles.backButton}>
          ← Back
        </button>
        
        <h1 className={styles.title}>Admin Dashboard</h1>
        <p className={styles.subtitle}>Manage your restaurant operations</p>
      </div>

      <div className={styles.grid}>
        {dashboardOptions.map((option) => (
          <AdminDashboardCard
            key={option.id}
            title={option.title}
            description={option.description}
            icon={option.icon}
            onClick={() => handleCardClick(option.route)}
          />
        ))}
      </div>
    </div>
  )
}
