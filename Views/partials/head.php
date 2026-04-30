<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<!-- Tailwind CSS -->
<script src="https://cdn.tailwindcss.com"></script>
<!-- Google Fonts: Inter -->
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<!-- Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

<script>
    tailwind.config = {
        theme: {
            extend: {
                fontFamily: {
                    sans: ['Inter', 'sans-serif'],
                },
                colors: {
                    primary: '#6e8efb',
                    secondary: '#a777e3',
                }
            }
        }
    }
</script>

<style>
    [v-cloak] { display: none; }
    .sidebar-transition { transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); }
    .table-responsive { overflow-x: auto; }
    .product-img { width: 60px; height: 60px; object-fit: cover; border-radius: 8px; }
</style>
