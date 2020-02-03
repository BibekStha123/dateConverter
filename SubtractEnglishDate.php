<?php 

namespace App\Utils;

use App\Utils\DateHelper;
use App\Utils\AddEnglishDate;


class SubtractEnglishDate
{
    private $date;
    private $dateHelper;

    public function __construct($date) {
        $this->date = $date;
        $this->dateHelper = new DateHelper();
    }

    //subtract days
    public function subDays($days, $date=null)
    {
        $dateDetail = $this->dateHelper->getDayMonthYear($date??$this->date);
        $day = $dateDetail['day'];
        $month = $dateDetail['month'];
        $year = $dateDetail['year'];

        $subtracted_days = $day - $days;

        $noOfDaysInMonth = $this->dateHelper->getDateTable($year, $month)->days_ad;

        if($subtracted_days < 1)
        {
            $new_days = $noOfDaysInMonth + $subtracted_days;
            $month--;
            if($month < 1)
            {
                $month = 12;
                $year--;
            }
            $new_date = $year .'/'. $month .'/'. $noOfDaysInMonth;
            $new_day = $noOfDaysInMonth - $new_days;
            return $this->subDays($new_day, $new_date);
        }

        return ($year .'/'. $month .'/'. $subtracted_days);
        
    }

    //subtract months
    public function subMonths($months, $date=null)
    {
        $dateDetail = $this->dateHelper->getDayMonthYear($date??$this->date);
        $day = $dateDetail['day'];
        $month = $dateDetail['month'];
        $year = $dateDetail['year'];

        $subtracted_months = $month - $months;

        if($subtracted_months < 1)
        {
            return $this->monthRecursion($year, $day, $subtracted_months);
        }

        //if day is less than or equal 1, decrease month and also decrease year if month is less than 1
        if($day <= 1)
        {
            $month--;
            $noOfDaysInMonth = $this->dateHelper->getDateTable($year, $month)->days_ad;
            $day = $noOfDaysInMonth;
            if($month < 1)
            {
                return $this->monthRecursion($year, $day, $month);               
            }
        }

        //if month is greater than 12, add the negative month
        if($subtracted_months > 12)
        {
            $subtracted_months = $month + $months;
            if($subtracted_months < 1)
            {
                return $this->monthRecursion($year, $day, $subtracted_months);
            }
        }

        //must be here, other wise day will be deducted again and again because of recursive function
        $day = $day - 1;

        return ($year .'/'. $subtracted_months .'/'. $day);
    }

    //calls the subMonths recursively
    public function monthRecursion($year, $day, $subtracted_months)
    {
        $new_date = $this->monthHelper($year, $day);
        return $this->subMonths($subtracted_months, $new_date);
    }

    //deducting year by 1 if month is less than 1
    public function monthHelper($year, $day)
    {
        $month = 12;
        $year--;
        $new_date = $year .'/'. $month .'/'. $day;
        return $new_date;
    }

    //subtracting year
    public function subYears($years, $date = null)
    {
        $dateDetail = $this->dateHelper->getDayMonthYear($date??$this->date);
        $day = $dateDetail['day'];
        $month = $dateDetail['month'];
        $year = $dateDetail['year'];

        $year = $year - $years;
        $month = $month - 1;
        $day = $day - 1;

        //decrease year by 1, if month is less than 1
        if($month < 1)
        {
            $year--;
            $month = 12;
            $noOfDaysInMonth = $this->dateHelper->getDateTable($year, $month)->days_ad;
            if($day > $noOfDaysInMonth)
            {
                $day = $noOfDaysInMonth;
            } elseif ($day < 1) {
                $new_date = $this->checkMonth($year, $month, $day);
                $year = $new_date[0];
                $month = $new_date[1];
                $day = $new_date[2];
            }
        }

        //decrease month by 1, if day is less than 1
        if($day < 1)
        {
            $new_date = $this->checkMonth($year, $month, $day);
            $year = $new_date[0];
            $newMonth = $new_date[1];
            $day = $new_date[2];
        }

        return ($year .'/'. $month .'/'. $day);
         
    }

    //decrease month by 1 if day is less than 1
    public function checkMonth($newYear, $newMonth, $newDay)
    {
        $newMonth--;
        if($newMonth < 1){
            $newYear--;
            $newMonth = 12;
            $noOfDaysInMonth = $this->dateHelper->getDateTable($newYear, $newMonth)->days_ad;
            $newDay = $noOfDaysInMonth;
        } else {
            $newDay = $this->dateHelper->getDateTable($newYear, $newMonth)->days_ad;
        }
        return [$newYear, $newMonth, $newDay];
    }

    //sub all
    public function subAll($years, $months, $days)
    {
        $dateAfterDays = $this->subDays($days); 
        $dateAfterMonths = $this->subMonths($months, $dateAfterDays);
        $dateAfterYears = $this->subYears($years, $dateAfterMonths);
        $englishDate = new AddEnglishDate($dateAfterYears);
        return $englishDate->addDays(1);
    }

    //sub months and years
    public function subYearsAndMonths($years, $months)
    {
        $dateAfterMonths = $this->subMonths($months);
        $dateAfterYears = $this->subYears($years, $dateAfterMonths);
        $englishDate = new AddEnglishDate($dateAfterYears);
        return $englishDate->addDays(1);
    }

    //sub days and years
    public function subYearsAndDays($years, $days)
    {
        $dateAfterDays = $this->subDays($days);
        $dateAfterYears = $this->subYears($years, $dateAfterDays);
        return $dateAfterYears;
    }

    //sub days and months
    public function subMonthsAndDays($months, $days)
    {
        $dateAfterDays = $this->subDays($days);
        $dateAfterMonths = $this->subMonths($months, $dateAfterDays);
        return $dateAfterMonths;
    }

}


