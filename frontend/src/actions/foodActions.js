"use server";
import {
  getFoods,
  getFood,
  getMostSoldFoods,
  createFood,
  updateFood,
  deleteFood,
} from "@/libs/api";

export const actionError = async (response) => {
  if (typeof response.error === "object") {
    const errorMessages = {};

    if (response.error.category_id) {
      errorMessages["category"] = response.error.category_id;
    }

    if (response.error.name) {
      errorMessages["name"] = response.error.name;
    }

    if (response.error.description) {
      errorMessages["description"] = response.error.description;
    }

    if (response.error.price) {
      errorMessages["price"] = response.error.price;
    }

    if (response.error.stock) {
      errorMessages["stock"] = response.error.stock;
    }

    if (response.error.image_url) {
      errorMessages["image"] = response.error.image_url;
    }

    return { error: errorMessages };
  }

  return { error: { error: response.error } };
};

export const getFoodsAction = async (queryParams = {}) => {
  try {
    const response = await getFoods(queryParams);

    if (response.error) {
      return { error: response.error };
    }

    return {
      data: response.data,
      pagination: {
        count: response.total,
        total_pages: Math.ceil(response.total / response.per_page),
        next: response.next_page_url ? new URL(response.next_page_url).search : null,
        previous: response.prev_page_url ? new URL(response.prev_page_url).search : null,
      },
    };
  } catch (error) {
    console.error(error);
    return { error: error.message || "An unexpected Error occured." };
  }
};

export const getMostSoldFoodsAction = async (queryParams = {}) => {
  try {
    const response = await getMostSoldFoods(queryParams);

    if (response.error) {
      return { error: response.error };
    }

    return {
      data: response.data,
      pagination: {
        count: response.total,
        total_pages: Math.ceil(response.total / response.per_page),
        next: response.next_page_url ? new URL(response.next_page_url).search : null,
        previous: response.prev_page_url ? new URL(response.prev_page_url).search : null,
      },
    };
  } catch (error) {
    console.error(error);
    return { error: error.message || "An unexpected Error occured." };
  }
};

export const getFoodAction = async (id) => {
  try {
    const response = await getFood(id);

    if (response.error) {
      return { error: response.error };
    }

    return { data: response };
  } catch (error) {
    console.error(error);
    return { error: error.message || "An unexpected Error occured" };
  }
};

export const createFoodAction = async (formData) => {
  try {
    const response = await createFood(formData);

    if (response.error) {
      return actionError(response);
    }

    return { success: response.success };
  } catch (error) {
    console.error(error);
    return { error: error.message || "An unexpected Error occured" };
  }
};

export const updateFoodAction = async (id, formData) => {
  try {
    let data, response;
    let image_url = formData.get("image_url")
    
    if (image_url.size > 0) {
      response = await updateFood(id, formData, true);
    } else {
      data = {
        ...(formData.get("category_id") && { category_id: formData.get("category_id") }),
        ...(formData.get("name") && { name: formData.get("name") }),
        ...(formData.get("description") && { description: formData.get("description") }),
        ...(formData.get("price") && { price: formData.get("price") }),
        ...(formData.get("stock") && { stock: formData.get("stock") }),
      };

      response = await updateFood(id, data);
    }

    if (response.error) {
      return actionError(response);
    }

    return { success: response.success };
  } catch (error) {
    console.error(error);
    return { error: error.message || "An unexpected Error occured" };
  }
};

export const deleteFoodAction = async (id) => {
  try {
    const response = await deleteFood(id);

    if (response.error) {
      return { error: response.error };
    }
    
    return { success: response.success };
  } catch (error) {
    console.error(error);
    return { error: error.message || "An unexpected Error occured" };
  }
};
