import { createCrudRoutes } from './crud.js'

export default createCrudRoutes({
  table: 'partners',
  requiredFields: ['name'],
})
