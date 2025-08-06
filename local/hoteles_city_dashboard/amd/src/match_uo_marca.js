define([], function() {

    return {
        init: function() {
            document.addEventListener('change', function(e) {
                var someNode = e.target.closest('.someclass');
                if (someNode) {
                    alert('It changed!');
                }
            });
        }
    };
});
