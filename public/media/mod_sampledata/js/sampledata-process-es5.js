(function () {
  'use strict';

  /**
   * @copyright  (C) 2018 Open Source Matters, Inc. <https://www.joomla.org>
   * @license    GNU General Public License version 2 or later; see LICENSE.txt
   */
  var SampleData = {
    inProgress: false
  };
  var sampledataAjax = function sampledataAjax(type, steps, step) {
    // Get variables
    var baseUrl = "index.php?option=com_ajax&format=json&group=sampledata&" + Joomla.getOptions('csrf.token') + "=1";
    var options = Joomla.getOptions('sample-data');

    // Create list
    var list = document.createElement('div');
    list.classList.add("sampledata-steps-" + type + "-" + step);
    list.setAttribute('role', 'region');
    list.setAttribute('aria-live', 'polite');

    // Create paragraph
    var para = document.createElement('p');
    para.classList.add('loader-image');
    para.classList.add('text-center');

    // Create image
    var img = document.createElement('img');
    img.setAttribute('src', options.icon);
    img.setAttribute('width', 30);
    img.setAttribute('height', 30);

    // Append everything
    para.appendChild(img);
    list.appendChild(para);
    document.querySelector(".sampledata-progress-" + type).appendChild(list);
    Joomla.request({
      url: baseUrl + "&type=" + type + "&plugin=SampledataApplyStep" + step + "&step=" + step,
      method: 'GET',
      perform: true,
      onSuccess: function onSuccess(resp) {
        // Remove loader image
        var loader = list.querySelector('.loader-image');
        loader.parentNode.removeChild(loader);
        var response = {};
        try {
          response = JSON.parse(resp);
        } catch (e) {
          Joomla.renderMessages({
            error: [Joomla.Text._('MOD_SAMPLEDATA_INVALID_RESPONSE')]
          }, ".sampledata-steps-" + type + "-" + step);
          SampleData.inProgress = false;
          return;
        }
        var progressClass = '';
        var success;
        if (response.success && response.data && response.data.length > 0) {
          var progress = document.querySelector(".sampledata-progress-" + type + " .progress-bar");

          // Display all messages that we got
          response.data.forEach(function (value) {
            if (value === null) {
              return;
            }

            // eslint-disable-next-line prefer-destructuring
            success = value.success;
            progressClass = success ? 'bg-success' : 'bg-danger';

            // Display success alert
            if (success) {
              Joomla.renderMessages({
                message: [value.message]
              }, ".sampledata-steps-" + type + "-" + step, false, 3000);
            } else {
              Joomla.renderMessages({
                error: [value.message]
              }, ".sampledata-steps-" + type + "-" + step, false);
            }
          });

          // Update progress
          progress.innerText = step + "/" + steps;
          progress.style.width = step / steps * 100 + "%";
          progress.setAttribute('aria-valuemin', 0);
          progress.setAttribute('aria-valuemax', 100);
          progress.setAttribute('aria-valuenow', step / steps * 100);
          progress.classList.add(progressClass);

          // Move on next step
          if (success && step <= steps) {
            var stepNew = step + 1;
            if (stepNew <= steps) {
              sampledataAjax(type, steps, stepNew);
            } else {
              var bar = document.querySelector(".sampledata-progress-" + type);
              bar.parentNode.removeChild(bar);
              Joomla.renderMessages({
                message: [Joomla.Text._('MOD_SAMPLEDATA_COMPLETED')]
              });
              window.scroll({
                top: 0,
                left: 0,
                behavior: 'smooth'
              });
              SampleData.inProgress = false;
            }
          }
        } else {
          // Display error alert
          Joomla.renderMessages({
            error: [Joomla.Text._('MOD_SAMPLEDATA_INVALID_RESPONSE')]
          });
          window.scroll({
            top: 0,
            left: 0,
            behavior: 'smooth'
          });
          SampleData.inProgress = false;
        }
      },
      onError: function onError() {
        Joomla.renderMessages({
          error: [Joomla.Text._('MOD_SAMPLEDATA_ERROR_RESPONSE')]
        });
        window.scroll({
          top: 0,
          left: 0,
          behavior: 'smooth'
        });
        SampleData.inProgress = false;
      }
    });
  };
  var sampledataApply = function sampledataApply(element) {
    var type = element.getAttribute('data-type');
    var steps = element.getAttribute('data-steps');

    // Check whether the work in progress or we already processed with current item
    if (SampleData.inProgress) {
      return;
    }
    if (element.getAttribute('data-processed')) {
      alert(Joomla.Text._('MOD_SAMPLEDATA_ITEM_ALREADY_PROCESSED'));
      SampleData.inProgress = false;
      return;
    }

    // Make sure that use run this not by random clicking on the page links
    // @todo use the CE Modal here
    if (!window.confirm(Joomla.Text._('MOD_SAMPLEDATA_CONFIRM_START'))) {
      // eslint-disable-next-line consistent-return
      return false;
    }

    // Turn on the progress container
    var progressElements = [].slice.call(document.querySelectorAll(".sampledata-progress-" + type));
    progressElements.forEach(function (progressElement) {
      progressElement.classList.remove('d-none');
    });
    element.setAttribute('data-processed', true);
    SampleData.inProgress = true;
    sampledataAjax(type, steps, 1);

    // eslint-disable-next-line consistent-return
    return false;
  };
  var sampleDataWrapper = document.getElementById('sample-data-wrapper');
  if (sampleDataWrapper) {
    var links = [].slice.call(sampleDataWrapper.querySelectorAll('.apply-sample-data'));
    links.forEach(function (link) {
      link.addEventListener('click', function (_ref) {
        var currentTarget = _ref.currentTarget;
        return sampledataApply(currentTarget);
      });
    });
  }

})();
