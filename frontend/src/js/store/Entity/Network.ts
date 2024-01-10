export enum RequestStatus {
  None,
  Updating,
  Success,
  Failed
}

export enum ErrorType {
  Unknown = 10,

  // Hakoniwa (~99)
  LackOfFunds,
  NotChanged,
  DuplicatedIslandName,
  DuplicatedOwnerName,

  // HTTP (100~)
  NotFound = 404,
  TooManyRequests = 429
}

export interface AjaxResult {
  status: RequestStatus
  error?: ErrorType
}
