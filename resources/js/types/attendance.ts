// resources/js/Types/Attendance.ts

export type AttendanceStatus =
  | 'present'
  | 'absent_unjustified'
  | 'absent_justified'
  | 'license'
  | 'holiday'
  | 'anomaly'

export interface Employee {
  id: number
  document: string
  full_name: string
  device_user_id: number
  // cuil?: string | null
  // area?: string | null
}

export interface LaborLink {
  id: number
  person: Employee
}

export interface DailySummary {
  id: number
  labor_link_id: number
  date: string
  status:
    | 'present'
    | 'absent_unjustified'
    | 'absent_justified'
    | 'license'
    | 'partial'
    | 'holiday'
    | 'anomaly'
  worked_minutes: number
  late_minutes: number
  early_leave_minutes: number
  has_license: boolean
  has_context_event: boolean
  anomalies: any[] | null
  notes: string | null
  // employee: Employee
  labor_link: LaborLink
}

