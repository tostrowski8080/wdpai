function toggleRole(userId, currentRole) {
    const newRole = (currentRole === 'admin') ? 'standard' : 'admin';

    fetch('/admin_update_role', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ id: userId, newRole: newRole })
    })
    .then(response => {
        if (response.ok) {
            const badge = document.getElementById('badge-' + userId);
            const btn = badge.parentElement.nextElementSibling.querySelector('button');
                    
            badge.innerText = newRole;
            badge.className = 'role-badge role-' + newRole;
                    
            btn.setAttribute('onclick', `toggleRole(${userId}, '${newRole}')`);
        }
    });
}