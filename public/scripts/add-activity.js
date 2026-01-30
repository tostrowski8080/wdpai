const customSelect = document.getElementById('custom-category-dropdown');
const selectTrigger = document.getElementById('custom-select-trigger');
const customOptions = document.querySelector('.custom-options');
const options = document.querySelectorAll('.custom-option');
const hiddenInput = document.getElementById('category_id_input');
const triggerText = selectTrigger.querySelector('.placeholder');

selectTrigger.addEventListener('click', function() {
    customSelect.classList.toggle('open');
});

options.forEach(option => {
    option.addEventListener('click', function() {
        const value = this.getAttribute('data-value');
        const color = this.getAttribute('data-color');
        const text = this.querySelector('.cat-name').innerText;
        const iconContent = this.querySelector('.option-content').innerHTML;

        hiddenInput.value = value;

        selectTrigger.style.color = color;
        selectTrigger.style.borderColor = color;
        selectTrigger.style.backgroundColor = hexToRgbA(color, 0.1);
        
        triggerText.innerHTML = iconContent;
        
        triggerText.classList.remove('placeholder');
        triggerText.classList.add('selected-value');

        customSelect.classList.remove('open');
    });
});
document.addEventListener('click', function(e) {
    if (!customSelect.contains(e.target)) {
        customSelect.classList.remove('open');
    }
});

if (hiddenInput.value) {
    const existingOption = document.querySelector(`.custom-option[data-value="${hiddenInput.value}"]`);
    if (existingOption) {
        const color = existingOption.getAttribute('data-color');
        const iconContent = existingOption.querySelector('.option-content').innerHTML;

        selectTrigger.style.color = color;
        selectTrigger.style.borderColor = color;
        selectTrigger.style.backgroundColor = hexToRgbA(color, 0.1);
        triggerText.innerHTML = iconContent;
        triggerText.classList.remove('placeholder');
    }
}

const recurringCheckbox = document.getElementById('recurring');
const recurrenceOptions = document.getElementById('recurrence-options');
const deadlineCheckbox = document.getElementById('deadline-toggle');
const manualScheduling = document.getElementById('manual-scheduling');
const deadlineScheduling = document.getElementById('deadline-scheduling');

if (recurringCheckbox) {
    recurringCheckbox.addEventListener('change', function() {
        if (this.checked) {
            recurrenceOptions.classList.remove('hidden');
        } else {
            recurrenceOptions.classList.add('hidden');
        }
    });
}

if (deadlineCheckbox) {
    deadlineCheckbox.addEventListener('change', function() {
        if (this.checked) {
            manualScheduling.classList.add('hidden');
            deadlineScheduling.classList.remove('hidden');
        } else {
            manualScheduling.classList.remove('hidden');
            deadlineScheduling.classList.add('hidden');
        }
    });
}

function hexToRgbA(hex, alpha){
    let c;
    if(/^#([A-Fa-f0-9]{3}){1,2}$/.test(hex)){
        c= hex.substring(1).split('');
        if(c.length== 3){
            c= [c[0], c[0], c[1], c[1], c[2], c[2]];
        }
        c= '0x'+c.join('');
        return 'rgba('+[(c>>16)&255, (c>>8)&255, c&255].join(',')+','+alpha+')';
    }
    return hex;
}