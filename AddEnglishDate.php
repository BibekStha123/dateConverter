<?php

namespace App\Utils;

use App\Utils\DateHelper;

class AddEnglishDate
{
    private $date;
    private $dateHelper;

    public function __construct($date) {
        $this->date = $date;
        $this->dateHelper = new DateHelper();
    }

    //only add days
    public function addDays($days, $date=null)
    {
        $dateData = explode('/', $date??$this->date);
        $given_day = $dateData[2];
        $year = $dateData[0];
        $month = $dateData[1];

        $total_days_in_month = $this->dateHelper->getDateTable($year, $month)->days_ad;
        
        if ($given_day > $total_days_in_month) {
            throw new Exception("Days exceeds total days given month", 1);
        }

        // $days_left_in_month = $total_days_in_month - $given_day;
    

        $addedDays = $given_day + $days;
        
        if ($addedDays > $total_days_in_month) {
            $extra_days = $addedDays - $total_days_in_month;
            $month++;
            if ($month > 12) {
                $month = $month - 12;
                $year++;
            }
            $newDate = $year .'/'. $month .'/'. '00';
            $newDayToadd = $extra_days - 1;
            return $this->addDays($newDayToadd, $newDate);
        }
        return $year .'/'. $month .'/'. $addedDays; 
    }

    //only add months
    public function addMonths($month_data, $date = null)
    {
        $dateData = explode('/', $date??$this->date);
        $given_day = $dateData[2];
        $year = $dateData[0];
        $month = $dateData[1];

        $newMonth = $month + $month_data;

        $newDay = $given_day - 1;

        if ($newMonth > 12) {
            $newMonth = $newMonth - 12;
            $year++;
            $newDate = $year .'/'. '01' .'/'. $given_day;
            return $this->addMonths($newMonth - 1, $newDate);
        }
        
        $total_days_in_month = $this->dateHelper->getDateTable($year, $newMonth)->days_ad;
    

        if ($newDay > $total_days_in_month) {
            $newDay = $total_days_in_month;
        }

        return $year .'/'. $newMonth .'/'. $newDay;

    }

    //only add years
    public function addYears($year_data, $date = null)
    {
        $dateData = explode('/', $date??$this->date);
        $given_day = $dateData[2];
        $year = $dateData[0];
        $month = $dateData[1];

        $newYear = $year + $year_data;
        $newMonth = $month - 1;
        $newDay = $given_day - 1;

        return $this->addYearHelper($newYear, $newMonth, $newDay);
    }

    //helper function for adding year
    public function addYearHelper($newYear, $newMonth, $newDay)
    {
        //decrease year 
        if ($newMonth < 1) {
            $newYear--;
            $newMonth = 12;

            $total_days_in_month = $this->dateHelper->getDateTable($newYear, $newMonth)->days_ad;
            if($newDay > $total_days_in_month){
                $newDay = $total_days_in_month;
            } elseif ($newDay < 1) {//decrease month 
                $new_date = $this->checkMonth($newYear, $newMonth, $newDay);
                $newYear = $new_date[0];
                $newMonth = $new_date[1];
                $newDay = $new_date[2];
            }
        }

        //decrease month
        if($newDay < 1)
        {
            $new_date = $this->checkMonth($newYear, $newMonth, $newDay);
            $newYear = $new_date[0];
            $newMonth = $new_date[1];
            $newDay = $new_date[2];
        }

        return ( $newYear .'/'. $newMonth .'/'. $newDay);
    }

    //decrease month by 1 if day is less than 1
    public function checkMonth($newYear, $newMonth, $newDay)
    {
        $newMonth--;
        if($newMonth < 1){
            $newYear--;
            $newMonth = 12;
            $total_days_in_month = $this->dateHelper->getDateTable($newYear, $newMonth)->days_ad;
            $newDay = $total_days_in_month;
        } else {
            $newDay = $this->dateHelper->getDateTable($newYear, $newMonth)->days_ad;
        }
        return [$newYear, $newMonth, $newDay];
    }

    //add all
    public function addAll($years, $months, $days)
    {
        $dateAfterDays = $this->addDays($days); 
        $dateAfterMonths = $this->addMonths($months, $dateAfterDays);
        $dateAfterYears = $this->addYears($years, $dateAfterMonths);
        return $this->addDays('1', $dateAfterYears);
    }

    //add months and years
    public function addYearsAndMonths($years, $months)
    {
        $dateAfterMonths = $this->addMonths($months);
        $dateAfterYears = $this->addYears($years, $dateAfterMonths);
        return $this->addDays('1', $dateAfterYears);
    }

    //add days and years
    public function addYearsAndDays($years, $days)
    {
        $dateAfterDays = $this->addDays($days);
        $dateAfterYears = $this->addYears($years, $dateAfterDays);
        return $dateAfterYears;
    }

    //add days and months
    public function addMonthsAndDays($months, $days)
    {
        $dateAfterDays = $this->addDays($days);
        $dateAfterMonths = $this->addMonths($months, $dateAfterDays);
        return $dateAfterMonths;
    }
}
