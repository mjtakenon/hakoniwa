import { IslandEnvironment } from './Island.js'

export interface Status {
  area: number
  development_points: number
  environment: IslandEnvironment
  foods: number
  foods_production_capacity: number
  funds: number
  funds_production_capacity: number
  population: number
  resources: number
  resources_production_capacity: number
  maintenance_number_of_people: number
  abandonment_turn: number
}
