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
  status: AttendanceStatus
  total_worked_minutes: number
  justified: boolean
  notes?: string | null

  // Si desde Laravel haces ->with('employee')
  employee?: Employee
}
