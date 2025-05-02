import api from '@/config/api'

const login = (credentials) => {
  return api.post('/auth/login', credentials)
}

const register = (userData) => {
  return api.post('/auth/register', userData)
}

const logout = () => {
  return api.post('/auth/logout')
}

const fetchUser = () => {
  return api.get('/auth/user')
}

export {
  login,
  register,
  logout,
  fetchUser
}
