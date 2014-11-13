<?php
/**
 * Student Id:  uf222ba  
 * Name:        Ulrika Falk
 * Mail:        uf222ba@student.lnu.se 
 * Date:        2014-11-11
 * Laboration:  Laboration 1, Webbteknik II
 */

class Course {
    // medlemsvariabler
    private $courseTitle;
    private $courseCode;
    private $courseUrl;
    private $coursePlanUrl;
    private $ingress;
    private $latestPostHeader;
    private $latestPostAuthor;
    private $postPublished;


    // konstruktor
    function __construct($courseTitle, $courseUrl) {
        $this->courseTitle = $courseTitle;
        $this->courseUrl = $courseUrl;
    }

    // funktioner
    public function getCourseTitle()
    {
        return $this->courseTitle;
    }

    public function setCourseTitle($inParameter)
    {
       $this->courseTitle = $inParameter;
    }

    public function getCourseCode()
    {
        return $this->courseCode;
    }

    public function setCourseCode($inParameter)
    {
        $this->courseCode = $inParameter;
    }

    public function getCourseUrl()
    {
        return $this->courseUrl;
    }

    public function setCourseUrl($inParameter)
    {
        $this->courseUrl = $inParameter;
    }

    public function getCoursePlanUrl()
    {
        return $this->coursePlanUrl;
    }

    public function setCoursePlanUrl($inParameter)
    {
        $this->coursePlanUrl = $inParameter;
    }

    public function getIngress()
    {
        return $this->ingress;
    }

    public function setIngress($inparameter)
    {
        $this->ingress = $inparameter;
    }

    public function getLatestPostHeader()
    {
        return $this->latestPostHeader;
    }

    public function setLatestPostHeader($inParameter)
    {
        $this->latestPostHeader = $inParameter;
    }

    public function getLatestPostAuthor()
    {
        return $this->latestPostAuthor;
    }

    public function setLatestPostAuthor($inParameter)
    {
        $this->latestPostAuthor = $inParameter;
    }

    public function getPostPublished()
    {
        return $this->postPublished;
    }

    public function setPostPublished($inParameter)
    {
        $this->postPublished = $inParameter;
    }
}