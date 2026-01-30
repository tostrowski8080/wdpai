const nameInput = document.getElementById('category-name');
const colorInput = document.getElementById('category-color');
const iconInput = document.getElementById('category-icon');

const previewPill = document.getElementById('preview-pill');
const previewText = document.getElementById('preview-text');
const previewDot = document.getElementById('preview-dot');
const previewIcon = document.getElementById('preview-icon');

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

function updatePreview() {
    previewText.innerText = nameInput.value || 'New Category';
    
    const color = colorInput.value;
    previewPill.style.color = color;
    previewPill.style.backgroundColor = hexToRgbA(color, 0.1);
    previewDot.style.backgroundColor = color;

    if (iconInput.value.trim() !== "") {
        previewIcon.style.display = 'inline';
        previewIcon.innerText = iconInput.value;
        previewDot.style.display = 'none';
    } else {
        previewIcon.style.display = 'none';
        previewDot.style.display = 'inline-block';
    }
}

if(nameInput && colorInput && iconInput) {
    nameInput.addEventListener('input', updatePreview);
    colorInput.addEventListener('input', updatePreview);
    iconInput.addEventListener('input', updatePreview);
}