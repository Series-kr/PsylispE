<?php
namespace App\Models;

class Attendance extends BaseModel {
    protected $table = 'attendance';

    public function recordCheckIn($employeeId, $date, $checkInTime) {
        $query = "INSERT INTO {$this->table} (tenant_id, employee_id, date, check_in, status) 
                  VALUES (?, ?, ?, ?, 'present')
                  ON DUPLICATE KEY UPDATE check_in = VALUES(check_in), status = 'present'";
        
        $this->execute($query, [$this->tenantId, $employeeId, $date, $checkInTime]);
    }

    public function recordCheckOut($employeeId, $date, $checkOutTime) {
        // Calculate working hours
        $attendance = $this->getAttendance($employeeId, $date);
        if ($attendance && $attendance['check_in']) {
            $checkIn = strtotime($attendance['check_in']);
            $checkOut = strtotime($checkOutTime);
            $workingHours = round(($checkOut - $checkIn) / 3600, 2); // Convert seconds to hours
            
            $query = "UPDATE {$this->table} 
                      SET check_out = ?, working_hours = ?, updated_at = NOW()
                      WHERE employee_id = ? AND date = ? AND tenant_id = ?";
            
            $this->execute($query, [$checkOutTime, $workingHours, $employeeId, $date, $this->tenantId]);
        }
    }

    public function getAttendance($employeeId, $date) {
        $query = "SELECT * FROM {$this->table} 
                  WHERE employee_id = ? AND date = ? AND tenant_id = ?";
        
        $stmt = $this->execute($query, [$employeeId, $date, $this->tenantId]);
        return $stmt->fetch();
    }

    public function getMonthlyAttendance($employeeId, $year, $month) {
        $startDate = "$year-$month-01";
        $endDate = date('Y-m-t', strtotime($startDate));

        $query = "SELECT * FROM {$this->table} 
                  WHERE employee_id = ? AND tenant_id = ? 
                  AND date BETWEEN ? AND ?
                  ORDER BY date";
        
        $stmt = $this->execute($query, [$employeeId, $this->tenantId, $startDate, $endDate]);
        return $stmt->fetchAll();
    }

    public function getAttendanceSummary($employeeId, $year, $month) {
        $startDate = "$year-$month-01";
        $endDate = date('Y-m-t', strtotime($startDate));

        $query = "SELECT 
                    COUNT(*) as total_days,
                    SUM(CASE WHEN status = 'present' THEN 1 ELSE 0 END) as present_days,
                    SUM(CASE WHEN status = 'absent' THEN 1 ELSE 0 END) as absent_days,
                    SUM(CASE WHEN status = 'late' THEN 1 ELSE 0 END) as late_days,
                    SUM(working_hours) as total_hours
                  FROM {$this->table} 
                  WHERE employee_id = ? AND tenant_id = ? 
                  AND date BETWEEN ? AND ?";
        
        $stmt = $this->execute($query, [$employeeId, $this->tenantId, $startDate, $endDate]);
        return $stmt->fetch();
    }
}