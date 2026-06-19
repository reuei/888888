import { createCrudRoutes } from './crud.js'

export default createCrudRoutes({
  table: 'slides',
  requiredFields: ['title'],
})
