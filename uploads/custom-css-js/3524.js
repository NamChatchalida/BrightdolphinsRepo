<!-- start Simple Custom CSS and JS -->
<script type="text/javascript">
(function () {

      /*
          1. Inject CSS which makes iframe invisible
      */
    
    var div = document.createElement('div'),
        ref = document.getElementsByTagName('base')[0] || 
              document.getElementsByTagName('script')[0];

    div.innerHTML = '&shy;<style> iframe { visibility: hidden; } </style>';

    ref.parentNode.insertBefore(div, ref);

        
    /*
        2. When window loads, remove that CSS, 
           making iframe visible again
    */
    
    window.onload = function() {
        div.parentNode.removeChild(div);
    }
    
})();</script>
<!-- end Simple Custom CSS and JS -->
