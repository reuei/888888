import { createCrudRoutes } from './crud.js'

export default createCrudRoutes({
  table: 'testimonials',
  requiredFields: ['author', 'content'],
})
