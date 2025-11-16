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
  legajo: string
  cuil?: string | null
  full_name: string
  area?: string | null
}

export interface DailySummary {
  id: number
  employee_id: number
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
}

