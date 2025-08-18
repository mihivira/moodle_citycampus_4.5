// utils.js
define(function() {
    'use strict';

    return {
        /**
         * Render course chart
         */
        renderCourseChart: function(course, info) {
            console.log('Rendering chart for:', info.id, 'Type:', info.chart);
            
            if(info.chart === 'bar-agrupadas' || info.chart === 'horizontalBar' || 
            info.chart === 'line' || info.chart === 'burbuja') {
                course.comparative_graph();
            } else if(info.chart === 'pie' || info.chart === 'bar') {
                course.individual_graph();
            } else {
                // Default to comparative graph
                course.comparative_graph();
            }
        },

        /**
         * Get percentages for course info
         * @param {Object} info - Course information object
         * @returns {Object} Updated info with percentage fields
         */
        

        getPercentages: function(info) {
            if (!info) return info;

            if (info.enrolled_users > 0) {
                info.approved_percentage = Math.round((info.approved_users / info.enrolled_users) * 100) + "%";
                info.not_approved_percentage = Math.round((info.not_approved_users / info.enrolled_users) * 100) + "%";
            } else {
                info.approved_percentage = "0%";
                info.not_approved_percentage = "0%";
            }

            return info;
        }
    };
});