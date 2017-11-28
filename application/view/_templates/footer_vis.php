
    <!-- JavaScript references contained in the footer -->
    <footer>

      <!-- define base location as a JavaScript variable -->
  		<script>
  			var baseURL = '<?= URL_WITH_INDEX_FILE; ?>';
  		</script>

      <!-- WorkloadCalendarView, Visualisation Handler, D3 library and jQuery -->
      <script src="<?= URL; ?>public/js/jquery-1.11.3.min.js" charset="utf-8"></script>
      <script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/jquery-ui.min.js"></script>
      <script src="<?= URL; ?>public/js/d3.min.js" charset="utf-8"></script>
      <script src="<?= URL; ?>public/js/WorkloadCalendarView.js" charset="utf-8"></script>
      <script src="<?= URL; ?>public/js/VisualisationHandler.js" charset="utf-8"></script>

    </footer>

  </body>
</html>
