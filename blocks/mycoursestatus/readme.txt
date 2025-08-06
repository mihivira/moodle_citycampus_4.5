
Mycoursestatus, Updated version 3.6, Release: September,2019

(A) Download mycoursestatus archive file and extract to /blocks folder. You could add only one instance.

(B) Visit Site Administration > Notifications page (admin/index.php). The plugin should get installed.

(C) mycoursestatus is a student block for a detailed course report. You can add this block:
    Internal course page - shows course module report.
    External course page - shows enrolled course report.

(D) Visit Site administration > Advanced Features: "Enable completion tracking".

(E) Although there are several completion settings in order to complete a course. This block is developed based on three conditions:
    (Note: Choose either one, Condition A / Condition B / Condition C at a time, try not to choose more than one condition.)

    (1) Condition A => Condition: Activity Completion
    (2) Condition B => Course grade
    (3) Condition C => Condition: Self Completion   

    (1) Condition A => Condition: Activity Completion
     ------------------------------------------------
        Description : A student must view a resource module and receive pass grade of an activity module, results a student has
        completed a course. Setting can be done from each module and course completion settings.

        Settings    : (a) Visit resource/activity module of any course.

                      (b) Resource Module: Activity Completion > choose completion tracking as "Show activity as complete when 
                                                                 conditions are met"
                                                               > enable require view as "Student must view this activity to 
                                                                 complete it" 

                      (c) Activity Module: Activity Completion > choose completion tracking as "Show activity as complete when
                                                                 conditions are met"
                                                               > enable require view as "Student must view this activity to 
                                                                 complete it" 
                                                               > enable require grade as "Student must receive a grade to
                                                                 complete this activity"
                                                               > input require minimum score to pass a module.

                      (d) Visit Course settings > Course Completion > Course completion tab: 
                          General > choose Completion requirements as "Course is complete when ALL Conditions are met". 
                          Condition: Activity completion > You could see a list of modules that y've selected for each module,
                          select the one you wish to 
                          choose or select all (All selected activities to be completed).
   
    (2) Condition B => Course grade   
     ------------------------------
        Description : A student must score a minimum grade to complete a course. Setting can be done from course completion
        settings.

        Settings    : Visit Course settings > Course Completion > Course completion tab: 
                      Condition: Course grade > enable and input required course grade.
                      By default, course grade is calculated from all attempted course modules. If user tries to attempt one module,
                      course grade calculates the average from attempted modules. A default course grade compares with student
                      scored course grade.
                                 
    (3) Condition C => Condition: Self Completion
     --------------------------------------------
        Description : All modules must be completed, where a student must view a resource module and receive pass grade of an
        activity module, results a student has completed a course. Setting can be done from each module only.
          
        Settings    : (a) Visit resource/activity module of any course.

                      (b) Resource Module: Activity Completion > choose completion tracking as "Show activity as complete when
                          conditions are met"
                                                               > enable require view as "Student must view this activity to
                                                                 complete it" 

                      (c) Activity Module: Activity Completion > choose completion tracking as "Show activity as complete when
                                                                 conditions are met"
                                                               > enable require view as "Student must view this activity to
                                                                 complete it" 
                                                               > enable require grade as "Student must receive a grade to
                                                                 complete this activity"
                                                               > input require minimum score to pass a module.
