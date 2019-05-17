<!-- Global Spark Object -->
<script>
    window.Spark = <?php echo json_encode(array_merge(
        Spark::scriptVariables(), [
            'baseUrl' => url('/'),
            'showLoader' => false,
            'defaultTimezone' => config('app.timezone'),
            'env' => env('APP_ENV'),
        ]
    )); ?>;
</script>
