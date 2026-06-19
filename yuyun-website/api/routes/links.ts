import { createCrudRoutes } from './crud.js'

export default createCrudRoutes({
  table: 'friend_links',
  requiredFields: ['name', 'url'],
})
