import { createCrudRoutes } from './crud.js'

export default createCrudRoutes({
  table: 'products',
  requiredFields: ['name'],
  jsonFields: ['features'],
})
