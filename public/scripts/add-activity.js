document.addEventListener('DOMContentLoaded', function() {
    const recurringToggle = document.getElementById('recurring');
    const recurrenceOptions = document.getElementById('recurrence-options');

    function toggleRecurrence() {
        if (recurringToggle.checked) {
            recurrenceOptions.classList.remove('hidden');
        } else {
            recurrenceOptions.classList.add('hidden');
        }
    }

    if(recurringToggle) {
        toggleRecurrence();
        recurringToggle.addEventListener('change', toggleRecurrence);
    }

    const deadlineToggle = document.getElementById('deadline-toggle');
    const manualSection = document.getElementById('manual-scheduling');
    const deadlineSection = document.getElementById('deadline-scheduling');

    function toggleDeadline() {
        if (deadlineToggle.checked) {
            manualSection.classList.add('hidden');
            deadlineSection.classList.remove('hidden');
        } else {
            manualSection.classList.remove('hidden');
            deadlineSection.classList.add('hidden');
        }
    }

    if(deadlineToggle) {
        toggleDeadline();
        deadlineToggle.addEventListener('change', toggleDeadline);
    }

    const dropdown = document.getElementById('custom-category-dropdown');
    const trigger = document.getElementById('custom-select-trigger');
    const hiddenInput = document.getElementById('category_id_input');
    const options = document.querySelectorAll('.custom-option');

    if (dropdown && trigger) {
            trigger.addEventListener('click', function(e) {
            e.stopPropagation();
            dropdown.classList.toggle('open');
        });

        options.forEach(option => {
            option.addEventListener('click', function() {
                const value = this.getAttribute('data-value');
                const htmlContent = this.querySelector('.option-content').innerHTML;
                
                hiddenInput.value = value;
                
                trigger.innerHTML = htmlContent + '<span class="material-icons-round arrow">expand_more</span>';
                
                options.forEach(opt => opt.classList.remove('selected'));
                this.classList.add('selected');

                dropdown.classList.remove('open');
            });
        });

        document.addEventListener('click', function(e) {
            if (!dropdown.contains(e.target) && !trigger.contains(e.target)) {
                dropdown.classList.remove('open');
            }
        });

        if (hiddenInput.value) {
            const preSelected = document.querySelector(`.custom-option[data-value="${hiddenInput.value}"]`);
            if (preSelected) {
                const htmlContent = preSelected.querySelector('.option-content').innerHTML;
                trigger.innerHTML = htmlContent + '<span class="material-icons-round arrow">expand_more</span>';
                preSelected.classList.add('selected');
            }
        }
    }
});