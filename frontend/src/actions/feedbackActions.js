"use server";
import {
  getFeedbacks,
  createFeedback,
  updateFeedback,
  deleteFeedback
} from "@/libs/api";

export const actionError = async (response) => {
  if (typeof response.error === "object") {
    const errorMessages = {};

    if (response.error.food_id) {
      errorMessages["food_id"] = response.error.food_id;
    }

    if (response.error.comment) {
      errorMessages["comment"] = response.error.comment;
    }

    return { error: errorMessages };
  }

  return { error: { error: response.error } };
};

export const getFeedbacksAction = async (food_id) => {
  try {
    const response = await getFeedbacks(food_id);

    if (response.error) {
      return { error: response.error };
    }

    return { data: response };
  } catch (error) {
    console.error(error);
    return { error: error.message || "An unexpected Error occured." };
  }
};

export const createFeedbackAction = async (formData) => {
  const food_id = formData.get("food_id");
  const comment = formData.get("comment");

  const data = {
    food_id,
    comment,
  };

  try {
    const response = await createFeedback(data);

    if (response.error) {
      return actionError(response);
    }

    return { success: response.success };
  } catch (error) {
    console.error(error);
    return { error: error.message || "An unexpected Error occured." };
  }
};

export const updateFeedbackAction = async (id, formData) => {
  const comment = formData.comment;

  const data = {
    comment,
  };

  try {
    const response = await updateFeedback(id, data);
    console.log(response);

    if (response.error) {
      return actionError(response);
    }

    return { success: response.success };
  } catch (error) {
    console.error(error);
    return { error: error.message || "An unexpected Error occured." };
  }
};


export const deleteFeedbackAction = async (id) => {
  try {
    const response = await deleteFeedback(id);

    if (response.error) {
      return { error: response.error };
    }
    
    return { success: "Feedback deleted successfully" };
  } catch (error) {
    console.error(error);
    return { error: error.message || "An unexpected Error occured." };
  }
};
