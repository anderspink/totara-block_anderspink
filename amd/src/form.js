define(['jquery', 'core/webapi', 'core/notification'], function($, WebApi, Notification) {
  return {
    init: function(selectedId, selectedType) {
      const _this = this;

      $(function() {
        const configTeamEle = $('select[name="config_team"]');

        _this.updateBriefingsAndFolders(configTeamEle.val(), selectedId, selectedType);

        configTeamEle.change(function() {
          _this.updateBriefingsAndFolders($(this).val(), selectedId, selectedType);
        });
      });
    },
    updateBriefingsAndFolders: function(teamId, selectedId, selectedType) {
      const _this = this;

      if (teamId === undefined) {
        teamId = 0;
      }

      WebApi.call({
        operationName: 'block_anderspink_get_apidata',
        variables: {teamid: teamId}
      }).then(function(data) {
        const body = data.block_anderspink_get_apidata;
        const briefingSelectEle = $('select[name="config_briefing"]');
        const boardSelectEle = $('select[name="config_board"]');

        // Clear for new options
        briefingSelectEle.find('option').remove();
        boardSelectEle.find('option').remove();

        // Add new options to select element
        body.boards.forEach(function(entry) {
          var element = '';
          var entryId = entry.id.split('|')[0];

          if (selectedType === 'board' && selectedId == entryId) {
            element = _this.generateOptionElement(entry.name, entry.id, 'selected');
          } else {
            element = _this.generateOptionElement(entry.name, entry.id);
          }

          boardSelectEle.append(element);
        });

        // Add new options to select element
        body.briefings.forEach(function(entry) {
          var element;
          var entryId = entry.id.split('|')[0];

          if (selectedType === 'briefing' && selectedId == entryId) {
            element = _this.generateOptionElement(entry.name, entry.id, 'selected');
          } else {
            element = _this.generateOptionElement(entry.name, entry.id);
          }

          briefingSelectEle.append(element);
        });
      }).catch(Notification.exception);
    },
    generateOptionElement: function(html, value, selected) {
      const optionElement = $('<option></option>').val(value).html(html);

      if (selected) {
        optionElement.prop('selected', true);
      }

      return optionElement;
    }
  };
});