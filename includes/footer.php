<?php

$footerText = $footerText ?? ('FoTo-project');
?>
<footer class="w-full mt-auto" style="background-color:#0B0B45; color:#FFFFFF;">
    <div class="max-w-4xl mx-auto px-4 py-4 text-sm text-center">
        &copy; <?= date('Y'); ?> <?= htmlspecialchars($footerText); ?>
    </div>
</footer>
</body>
</html>
