import api from '@/config/api'

const fetchGroups = () => {
  return api.get('/groups')
}

const createGroup = (groupData) => {
  return api.post('/groups/store', groupData)
}

const updateGroup = (groupId, groupData) => {
  return api.patch(`/groups/update/${groupId}`, groupData)
}

const deleteGroup = (groupId) => {
  return api.delete(`/groups/delete/${groupId}`)
}

export {
  fetchGroups,
  createGroup,
  updateGroup,
  deleteGroup
}
