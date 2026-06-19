import { createCrudRoutes } from './crud.js'

export default createCrudRoutes({
  table: 'certificates',
  requiredFields: ['title'],
})
