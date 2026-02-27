</main>
<footer class="text-center text-sm text-gray-500 py-6">© FoTo</footer>
<script>
document.addEventListener('click', function(e){
    if (e.target && e.target.matches('.toggle-password')) {
        const input = document.querySelector(e.target.getAttribute('data-target'));
        if (input) input.type = input.type === 'password' ? 'text' : 'password';
    }
});
</script>
</body>
</html>
