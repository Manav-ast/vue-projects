// Common validation utility functions

/**
 * Validates if a value is not empty
 * @param {*} value - The value to check
 * @returns {string|null} Error message if invalid, null if valid
 */
export const validateRequired = (value) => {
  if (!value && value !== 0) {
    return 'This field is required'
  }
  return null
}

/**
 * Validates if a value is a positive number
 * @param {number} value - The number to validate
 * @returns {string|null} Error message if invalid, null if valid
 */
export const validatePositiveNumber = (value) => {
  if (isNaN(value) || value <= 0) {
    return 'Please enter a valid positive number'
  }
  return null
}

/**
 * Validates email format
 * @param {string} email - The email to validate
 * @returns {string|null} Error message if invalid, null if valid
 */
export const validateEmail = (email) => {
  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/
  if (!emailRegex.test(email)) {
    return 'Please enter a valid email address'
  }
  return null
}

/**
 * Validates minimum length of a string
 * @param {string} value - The string to validate
 * @param {number} minLength - Minimum required length
 * @returns {string|null} Error message if invalid, null if valid
 */
export const validateMinLength = (value, minLength) => {
  if (value.length < minLength) {
    return `Must be at least ${minLength} characters long`
  }
  return null
}

/**
 * Validates maximum length of a string
 * @param {string} value - The string to validate
 * @param {number} maxLength - Maximum allowed length
 * @returns {string|null} Error message if invalid, null if valid
 */
export const validateMaxLength = (value, maxLength) => {
  if (value.length > maxLength) {
    return `Must not exceed ${maxLength} characters`
  }
  return null
}

/**
 * Validates a date is not in the future
 * @param {string|Date} date - The date to validate
 * @returns {string|null} Error message if invalid, null if valid
 */
export const validatePastDate = (date) => {
  const inputDate = new Date(date)
  const today = new Date()

  if (inputDate > today) {
    return 'Date cannot be in the future'
  }
  return null
}

/**
 * Combines multiple validators
 * @param {*} value - The value to validate
 * @param {Array<Function>} validators - Array of validator functions
 * @returns {string|null} First error message encountered, or null if all valid
 */
export const validateAll = (value, validators) => {
  for (const validator of validators) {
    const error = validator(value)
    if (error) return error
  }
  return null
}
