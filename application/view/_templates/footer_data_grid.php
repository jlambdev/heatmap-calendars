
    <!-- JavaScript references contained in the footer -->
    <footer>

      <!-- define base location as a JavaScript variable -->
  		<script>
  			var baseURL = '<?= URL_WITH_INDEX_FILE; ?>';
  		</script>

      <!-- JS for Editable Grid, including data management handler + jQuery -->
      <script src="<?= URL; ?>public/js/jquery-1.11.3.min.js" charset="utf-8"></script>
      <script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/jquery-ui.min.js"></script>
      <script src="<?= URL; ?>public/js/EditableGrid.js" charset="utf-8"></script>
      <script src="<?= URL; ?>public/js/editablegrid_editors.js" charset="utf-8"></script>
      <script src="<?= URL; ?>public/js/editablegrid_renderers.js" charset="utf-8"></script>
      <script src="<?= URL; ?>public/js/editablegrid_validators.js" charset="utf-8"></script>
      <script src="<?= URL; ?>public/js/editablegrid_utils.js" charset="utf-8"></script>
      <script src="<?= URL; ?>public/js/DataMgtHandler.js" charset="utf-8"></script>

    </footer>

  </body>
</html>
