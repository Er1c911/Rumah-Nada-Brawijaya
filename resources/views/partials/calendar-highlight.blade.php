<style>
    /* dynamic highlight styles */
    .today-header {
        background: #ffffff !important;
        color: #000000 !important;
    }
    .today-cell {
        background: #1b1b1b !important;
        color: #ffffff !important;
    }
</style>

<script>
    (function(){
        function updateHighlight(){
            try{
                const d = new Date();
                const today = d.getFullYear().toString().padStart(4,'0') + '-' + (d.getMonth()+1).toString().padStart(2,'0') + '-' + d.getDate().toString().padStart(2,'0'); // local YYYY-MM-DD
                const elems = document.querySelectorAll('[data-date]');
                elems.forEach(el => {
                    const d = el.getAttribute('data-date');
                    if(!d) return;
                    if(d === today){
                        // header or cell
                        if(el.tagName === 'TH'){
                            el.classList.add('today-header');
                        } else {
                            el.classList.add('today-cell');
                        }
                    } else {
                        el.classList.remove('today-header');
                        el.classList.remove('today-cell');
                    }
                });
            }catch(e){ console.error('calendar highlight', e); }
        }

        document.addEventListener('DOMContentLoaded', function(){
            updateHighlight();
            // check every minute in case user keeps page open across midnight
            setInterval(updateHighlight, 60 * 1000);
        });
    })();
</script>
