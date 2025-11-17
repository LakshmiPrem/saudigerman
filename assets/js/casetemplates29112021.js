  Dropzone.options.projectFilesUpload = false;
  Dropzone.options.projectExpenseForm = false;

  var expenseDropzone;
  $(function() {

      $('.project-tabs-and-opts-toggler').on('click', function(e) {
          e.preventDefault();
          slideToggle('.project-menu-panel');
          slideToggle('.project-toggler-opts');
      });

      init_ajax_search('customer', '#clientid_copy_project.ajax-search');

      // remove the divider for project actions in case there is no other li except for pin project
      $('ul.project-actions li:first-child').next('li.divider').remove();

      var file_id = get_url_param('file_id');
      if (file_id) {
          view_project_file(file_id, project_id);
      }

      // Fix for shortcuts in discussions textarea/contenteditable - jquery-comments plugin
      var $discussionsContentEditable = $('#project_file_data,#discussion-comments');
      $discussionsContentEditable.on('focus', '[contenteditable="true"]', function() {
          $.Shortcuts.stop();
      });
      $discussionsContentEditable.on('focusout', '[contenteditable="true"]', function() {
          $.Shortcuts.start();
      });

      $('body').on('show.bs.modal', '._project_file', function() {
          discussion_comments('#project-file-discussion', discussion_id, 'file');
      });

      $('body').on('shown.bs.modal', '#milestone', function() {
          $('#milestone').find('input[name="name"]').focus();
      });


      initDataTable('.table-credit-notes', admin_url + 'credit_notes/table?case_id=' + project_id, ['undefined'], ['undefined'], undefined, [0, 'DESC']);

      if ($('#timesheetsChart').length > 0 && typeof(project_overview_chart) != 'undefined') {
          var chartOptions = {
              type: 'bar',
              data: {},
              options: {
                  responsive: true,
                  maintainAspectRatio: false,
                  tooltips: {
                      enabled: true,
                      mode: 'single',
                      callbacks: {
                          label: function(tooltipItems, data) {
                              return decimalToHM(tooltipItems.yLabel);
                          }
                      }
                  },
                  scales: {
                      yAxes: [{
                          ticks: {
                              beginAtZero: true,
                              min: 0,
                              userCallback: function(label, index, labels) {
                                  return decimalToHM(label);
                              },
                          }
                      }],
                  },
              }
          };
          chartOptions.data = project_overview_chart.data;
          var ctx = document.getElementById("timesheetsChart");
          timesheetsChart = new Chart(ctx, chartOptions);
      }
      milestones_kanban();
      $('#project_top').on('change', function() {
          var val = $(this).val();
          var __project_group = get_url_param('group');
          if (__project_group) {
              __project_group = '?group=' + __project_group;
          } else {
              __project_group = '';
          }
          window.location.href = admin_url + 'casetemplates/view/' + val + __project_group;
      });

      if (typeof(Dropbox) != 'undefined' && $('#dropbox-chooser').length > 0) {
          document.getElementById("dropbox-chooser").appendChild(Dropbox.createChooseButton({
              success: function(files) {
                  $.post(admin_url + 'casediary/add_external_file', {
                      files: files,
                      project_id: project_id,
                      external: 'dropbox',
                      visible_to_customer: $('#pf_visible_to_customer').prop('checked')
                  }).done(function() {
                      var location = window.location.href;
                      window.location.href = location.split('?')[0] + '?group=project_files';
                  });
              },
              linkType: "preview",
              extensions: app_allowed_files.split(','),
          }));
      }

      $('body').on('click', '.milestone-column .cpicker,.milestone-column .reset_milestone_color', function(e) {
          e.preventDefault();
          var color = $(this).data('color');
          var invoker = $(this);
          var milestone_id = invoker.parents('.milestone-column').data('col-status-id');
          $.post(admin_url + 'casediary/change_milestone_color', {
              color: color,
              milestone_id: milestone_id
          }).done(function() {
              // Reset color needs reload
              if (color == '') {
                  window.location.reload();
              } else {
                  var $parent = invoker.parents('.milestone-column');
                  $parent.find('.reset_milestone_color').removeClass('hide');
                  $parent.find('.panel-heading').addClass('color-white').removeClass('task-phase');
                  $parent.find('.edit-milestone-phase').addClass('color-white');
              }
          })
      });

      if ($('#project-files-upload').length > 0) {
          new Dropzone('#project-files-upload', $.extend({}, _dropzone_defaults(), {
              paramName: "file",
              uploadMultiple: true,
              parallelUploads: 20,
              maxFiles: 20,
              accept: function(file, done) {
                  done();
              },
              success: function(file, response) {
                  if (this.getUploadingFiles().length === 0 && this.getQueuedFiles().length === 0) {
                      window.location.href = admin_url + 'casediary/view/' + project_id + '?group=project_files';
                  }
              },
              sending: function(file, xhr, formData) {
                  formData.append("visible_to_customer", $('input[name="visible_to_customer"]').prop('checked'));
              }
          }));
      }


      

      if ($('#project-expense-form').length > 0) {
          expenseDropzone = new Dropzone("#project-expense-form", $.extend({}, _dropzone_defaults(), {
              autoProcessQueue: false,
              clickable: '#dropzoneDragArea',
              previewsContainer: '.dropzone-previews',
              addRemoveLinks: true,
              maxFiles: 1,
              success: function(file, response) {
                  if (this.getUploadingFiles().length === 0 && this.getQueuedFiles().length === 0) {
                      window.location.reload();
                  }
              }
          }));
      }

      _validate_form($('#project-expense-form'), {
          category: 'required',
          date: 'required',
          amount: 'required',
          currency: 'required'
      }, projectExpenseSubmitHandler);

     /* gantt = $("#gantt").gantt({
          source: gantt_data,
          itemsPerPage: 25,
          months: JSON.parse(months_json),
          navigate: 'scroll',
          onRender: function() {
              var rm = $('#gantt .leftPanel .name .fn-label:empty').parents('.name').css('background', 'initial');
              $('#gantt .leftPanel .spacer').html('<span class="gantt_project_name"><i class="fa fa-cubes"></i> ' + $('.project-name').text() + '</span>');
              var _percent = $('input[name="project_percent"]').val();
              $('#gantt .leftPanel .spacer').append('<div style="padding:10px 20px 10px 20px;"><div class="progress mtop5 progress-bar-mini"><div class="progress-bar progress-bar-success no-percent-text" role="progressbar" aria-valuenow="' + _percent + '" aria-valuemin="0" aria-valuemax="100" style="width: 0%" data-percent="' + _percent + '"></div></div></div>');
              init_progress_bars();
          },
          onItemClick: function(data) {
              init_task_modal(data.task_id);
          },
          onAddClick: function(dt, rowId) {
              var fmt = new DateFormatter();
              var d0 = new Date(+dt);
              var d1 = fmt.formatDate(d0, app_date_format);
              new_task(admin_url + 'tasks/task?rel_type=casediary&rel_id=' + project_id + '&start_date=' + d1);
          }
      });*/
      // Expenses additional server params
      var Expenses_ServerParams = {};
      $.each($('._hidden_inputs._filters input'), function() {
          Expenses_ServerParams[$(this).attr('name')] = '[name="' + $(this).attr('name') + '"]';
      });

      _table_api = initDataTable('.table-project-expenses', admin_url + 'casediary/expenses/' + project_id, 'undefined', 'undefined', Expenses_ServerParams, [4, 'DESC']);

      if (_table_api) {
          _table_api.column(0).visible(false, false).columns.adjust();
      }

      init_rel_temp_tasks_table(project_id, 'casetemplates');
      initDataTable('.table-notes', admin_url + 'casediary/notes/' + project_id, [4], [4], 'undefined', [1, 'DESC']);


      var Timesheets_ServerParams = {};
      $.each($('._hidden_inputs._filters.timesheets_filters input'), function() {
          Timesheets_ServerParams[$(this).attr('name')] = '[name="' + $(this).attr('name') + '"]';
      });

      initDataTable('.table-timesheets', admin_url + 'casediary/timesheets/' + project_id, [8], [8], Timesheets_ServerParams, [3, 'DESC']);
      initDataTable('.table-project-discussions', admin_url + 'casediary/discussions/' + project_id, [4], [4], 'undefined', [1, 'DESC']);

      var headers_hearings = $('.table-project-hearings').find('th');
      var not_sortable_casediary = (headers_hearings.length - 1);
      initDataTable('.table-project-hearings', admin_url+'hearing/table', [not_sortable_casediary], [not_sortable_casediary],'',[0,'ASC']);
      
      _validate_form($('#milestone_form'), {
          name: 'required',
          //due_date: 'required'
      });

      _validate_form($('#discussion_form'), {
          subject: 'required',meeting_date : 'required',
      }, manage_discussion);

      var timesheet_rules = {};
      var time_sheets_form_elements = $('#timesheet_form').find('select');
      $.each(time_sheets_form_elements, function() {
          var name = $(this).attr('name');
          timesheet_rules[name] = 'required';
      });

      var validation_timesheet_duration = {
          required: {
              depends: function(element) {
                  if ($('.timesheet-date-toggler-text').is(':visible')) {
                      return false;
                  }
                  var label = $('label[for="timesheet_duration"]');
                  if (label.length > 0 && label.find('.req').length == 0) {
                      label.prepend('<small class="req text-danger">* </small>');
                  }
                  return true;
              }
          }
      }
      timesheet_rules['start_time'] = validation_timesheet_duration;
      timesheet_rules['end_time'] = validation_timesheet_duration;
      timesheet_rules['timesheet_duration'] = {
          required: {
              depends: function(element) {
                  if (!$('.timesheet-date-toggler-text').is(':visible')) {
                      return false;
                  }
                  return true;
              }
          }
      }
      _validate_form($('#timesheet_form'), timesheet_rules, manage_timesheets);

      $('#discussion').on('hidden.bs.modal', function(event) {
          var $d = $('#discussion');
          $d.find('input[name="subject"]').val('');
          $d.find('input[name="location"]').val('');
          $d.find('textarea[name="description"]').val('');
          $d.find('input[name="show_to_customer"]').prop('checked', true);
          $d.find('.add-title').removeClass('hide');
          $d.find('.edit-title').removeClass('hide');
      });

      $('#milestone').on('hidden.bs.modal', function(event) {
          $('#additional_milestone').html('');
          $('#milestone input[name="due_date"]').val('');
          $('#milestone input[name="name"]').val('');
          $('#milestone input[name="milestone_order"]').val($('.table-milestones tbody tr').length + 1);
          $('#milestone textarea[name="description"]').val('');
          $('#milestone input[name="description_visible_to_customer"]').prop('checked', false);
          $('#milestone .add-title').removeClass('hide');
          $('#milestone .edit-title').removeClass('hide');
      });

      $('#timesheet').on('hidden.bs.modal', function(event) {
          var $t = $('#timesheet');
          $t.find('select[name="timesheet_staff_id"]').removeAttr('data-staff_id');
          $t.find('select[name="timesheet_staff_id"]').empty();
          $t.find('select[name="timesheet_staff_id"]').selectpicker('refresh');
          $t.find('select[name="timesheet_task_id"]').selectpicker('val', '');
          $t.find('textarea[name="note"]').val('');
          $t.find('#timesheet_duration').val('');
          $t.find('#tags').tagit('removeAll');
          $('input[name="timer_id"]').val('');
      });

      $('#timesheet select[name="timesheet_task_id"]').on('change', function() {
          var select_staff = $('#timesheet select[name="timesheet_staff_id"]');
          var _task_id = $(this).val();
          if (_task_id == '') {
              select_staff.html('');
              select_staff.selectpicker('refresh');
              return;
          }
          var staff_id;
          if (select_staff.attr('data-staff_id')) {
              staff_id = select_staff.attr('data-staff_id');
          }
          requestGet('casediary/timesheet_task_assignees/' + _task_id + '/' + project_id + '/' + staff_id).done(function(response) {
              select_staff.html(response);
              select_staff.selectpicker('refresh');
          });
      });

      $('input[name="tasks"].copy').on('change', function() {
          var checked = $(this).prop('checked');
          if (checked) {
              var copy_assignees = $('input[name="task_include_assignees"]').prop('checked');
              var copy_followers = $('input[name="task_include_followers"]').prop('checked');
              if (copy_assignees || copy_followers) {
                  $('input[name="members"].copy').prop('checked', true);
              }
              $('.copy-project-tasks-status-wrapper').removeClass('hide');
          } else {
              $('.copy-project-tasks-status-wrapper').addClass('hide');
          }
      });

      $('input[name="task_include_assignees"],input[name="task_include_followers"]').on('change', function() {
          var checked = $(this).prop('checked');
          if (checked == true) {
              $('input[name="members"].copy').prop('checked', true);
          }
      });

      $('body').on('change', '#project_invoice_select_all_tasks,#project_invoice_select_all_expenses', function() {
          var checked = $(this).prop('checked');
          var name_selector;
          if ($(this).hasClass('invoice_select_all_expenses')) {
              name_selector = 'input[name="expenses[]"]';
          } else {
              name_selector = 'input[name="tasks[]"]';
          }
          if (checked == true) {
              $(name_selector).not(':disabled').prop('checked', true);
          } else {
              $(name_selector).not(':disabled').prop('checked', false);
          }
      });

      $('body').on('change', 'input[name="invoice_data_type"]', function() {
          var val = $(this).val();
          if (val == 'timesheets_individualy') {
              $('#timesheets_bill_include_notes').removeClass('hide');
          } else {
              $('#timesheets_bill_include_notes').addClass('hide');
          }
      });

      $('input[name="members"].copy').on('change', function() {
          var checked = $(this).prop('checked');
          var checked_tasks = $('input[name="tasks"].copy').prop('checked');
          if (!checked) {
              if (checked_tasks) {
                  $('input[name="task_include_assignees"]').prop('checked', false);
                  $('input[name="task_include_followers"]').prop('checked', false);
              }
          } else {
              if (checked_tasks) {
                  $('input[name="task_include_assignees"]').prop('checked', true);
                  $('input[name="task_include_followers"]').prop('checked', true);
              }
          }
      });
  });

  function milestones_switch_view() {
      $('#milestones-table').toggleClass('hide');
      $('.project-milestones-kanban').toggleClass('hide');
      if (!$.fn.DataTable.isDataTable('.table-milestones')) {
          initDataTable('.table-milestones', admin_url + 'casediary/milestones/' + project_id, [2], [0]);
      }
  }

  function manage_discussion(form) {
      var data = $(form).serialize();
      var url = form.action;
      $.post(url, data).done(function(response) {
          response = JSON.parse(response);
          if (response.success == true) {
              alert_float('success', response.message);
          }
          $('.table-project-discussions').DataTable().ajax.reload(null, false);
          $('#discussion').modal('hide');
          $('#discussion_form').find('button[type="submit"]').button('reset');
      });
      return false;
  }

  function manage_timesheets(form) {
      var data = $(form).serialize();
      var url = form.action;
      $.post(url, data).done(function(response) {
          response = JSON.parse(response);
          if (response.success == true) {
              alert_float('success', response.message);
          } else {
              alert_float('warning', response.message);
          }
          setTimeout(function() {
              window.location.reload();
          }, 1000);
      });
  }

  function edit_timesheet(invoker, id) {
      $('#timesheet select[name="timesheet_staff_id"]').attr('data-staff_id', $(invoker).attr('data-timesheet_staff_id'));
      $('select[name="timesheet_task_id"]').selectpicker('val', $(invoker).attr('data-timesheet_task_id'));
      $('input[name="timer_id"]').val(id);
      $('input[name="start_time"]').val($(invoker).attr('data-start_time'));
      $('input[name="end_time"]').val($(invoker).attr('data-end_time'));
      $('#timesheet textarea[name="note"]').val($(invoker).attr('data-note'));
      $('select[name="timesheet_task_id"]').change();

      $('#timesheet').modal('show');
      // causing problems with ui dropdown goes to top left side when modal is shown
      setTimeout(function() {
          var timesheetTags = $(invoker).attr('data-tags').split(',');
          for (var i in timesheetTags) {
              $('#timesheet #tags').tagit('createTag', timesheetTags[i]);
          }
      }, 500);
  }

 

  function new_discussion() {
      $('#discussion').modal('show');
      $('#discussion .edit-title').addClass('hide');
  }

  function new_milestone() {
      $('#milestone').modal('show');
      $('#milestone .edit-title').addClass('hide');
  }

  function new_timesheet() {
      $('#timesheet').modal('show');
  }

  function edit_milestone(invoker, id) {

      var description_visible_to_customer = $(invoker).data('description-visible-to-customer');
      if (description_visible_to_customer == 1) {
          $('input[name="description_visible_to_customer"]').prop('checked', true);
      } else {
          $('input[name="description_visible_to_customer"]').prop('checked', false);
      }
      $('#additional_milestone').append(hidden_input('id', id));
      $('#milestone input[name="name"]').val($(invoker).data('name'));
      $('#milestone input[name="due_date"]').val($(invoker).data('due_date'));
      $('#milestone input[name="milestone_order"]').val($(invoker).data('order'));
      $('#milestone textarea[name="description"]').val($(invoker).data('description'));
      $('#milestone').modal('show');
      $('#milestone .add-title').addClass('hide');
  }

  function edit_discussion(invoker, id) {
      $('#additional_discussion').append(hidden_input('id', id));
      $('#discussion input[name="subject"]').val($(invoker).data('subject'));
      $('#discussion input[name="location"]').val($(invoker).data('location'));
      $('#discussion input[name="meeting_date"]').val($(invoker).data('meetingdate'));
      $('#discussion textarea[name="description"]').val($(invoker).data('description'));
      var checked = $(invoker).data('show-to-customer') == 0 ? false : true;
      $('#discussion input[name="show_to_customer"]').prop('checked', checked);
      $('#discussion').modal('show');
      $('#discussion .add-title').addClass('hide');
  }

  function mass_stop_timers(only_billable) {
      requestGetJSON('casediary/mass_stop_timers/' + project_id + '/' + only_billable).done(function(response) {
          alert_float(response.type, response.message);
          setTimeout(function() {
              $('body').find('.modal-backdrop').eq(0).remove();
              init_timers();
              reload_tasks_tables();
              pre_invoice_project();
          }, 500);
      });
  }

  function pre_invoice_project() {
      requestGet('casediary/get_pre_invoice_project_info/' + project_id).done(function(response) {
          $('#pre_invoice_project').html(response);
          $('#pre_invoice_project_settings').modal('show');
      });
  }

  function invoice_project(project_id) {
      $('#pre_invoice_project_settings').modal('hide');
      var data = {};

      data.type = $('input[name="invoice_data_type"]:checked').val();
      data.timesheets_include_notes = $('input[name="timesheets_include_notes"]:checked').val();

      data.project_id = project_id;

      data.tasks = $("#tasks_who_will_be_billed input:checkbox:checked").map(function() {
          return $(this).val();
      }).get();

      data.expenses = $("#expenses_who_will_be_billed .expense-to-bill input:checkbox:checked").map(function() {
          return $(this).val();
      }).get();

      data.expenses_add_note = $("#expenses_who_will_be_billed .expense-add-note input:checkbox:checked").map(function() {
          return $(this).val();
      }).get();

      data.expenses_add_name = $("#expenses_who_will_be_billed .expense-add-name input:checkbox:checked").map(function() {
          return $(this).val();
      }).get();

      $.post(admin_url + 'casediary/get_invoice_project_data/', data).done(function(response) {
          $('#invoice_project').html(response);
          $('#invoice-project-modal').modal({
              show: true,
              backdrop: 'static'
          });
      });
  }

  function delete_project_discussion(id) {
      if (confirm_delete()) {
          requestGetJSON('casediary/delete_discussion/' + id).done(function(response) {
              alert_float(response.alert_type, response.message);
              $('.table-project-discussions').DataTable().ajax.reload(null, false);
          });
      }
  }

  function projectExpenseSubmitHandler(form) {
      $.post(form.action, $(form).serialize()).done(function(response) {
          response = JSON.parse(response);
          if (response.expenseid) {
              if (typeof(expenseDropzone) !== 'undefined') {
                  if (expenseDropzone.getQueuedFiles().length > 0) {
                      expenseDropzone.options.url = admin_url + 'expenses/add_expense_attachment/' + response.expenseid;
                      expenseDropzone.processQueue();
                  } else {
                      window.location.assign(response.url);
                  }
              } else {
                  window.location.assign(response.url);
              }
          } else {
              window.location.assign(response.url);
          }
      });
      return false;
  }

  function view_project_file(id, $project_id) {
      $('#project_file_data').empty();
      $("#project_file_data").load(admin_url + 'casediary/file/' + id + '/' + project_id, function(response, status, xhr) {
          if (status == "error") {
              alert_float('danger', xhr.statusText);
          }
      });
  }

  function update_file_data(id) {
      var data = {};
      data.id = id;
      data.subject = $('body input[name="file_subject"]').val();
      data.description = $('body textarea[name="file_description"]').val();
      data.document_title = $('body input[name="document_title"]').val();
      data.issue_date = $('body input[name="issue_date"]').val();
      data.expiry_date = $('body input[name="expiry_date"]').val();
      $.post(admin_url + 'casediary/update_file_data/', data);
  }

  function project_mark_as_modal(status_id, $project_id) {
      $('#mark_tasks_finished_modal').modal('show');
      $('#project_mark_status_confirm').attr('data-status-id', status_id);
      $('#project_mark_status_confirm').attr('data-project-id', project_id);
      var $projectMarkedasFinishedInput = $('#project_marked_as_finished_email_to_contacts');
      if (status_id == 4) {
          if ($projectMarkedasFinishedInput.length > 0) {
              $projectMarkedasFinishedInput.parents('.project_marked_as_finished').removeClass('hide');
          }
      } else {
          if ($projectMarkedasFinishedInput.length > 0) {
              $projectMarkedasFinishedInput.prop('checked', false);
              $projectMarkedasFinishedInput.parents('.project_marked_as_finished').addClass('hide');
          }
      }
  }

  function project_files_bulk_action(e) {
      if (confirm_delete()) {
          var mass_delete = $('#mass_delete').prop('checked');
          var ids = [];
          var data = {};
          if (mass_delete == false || typeof(mass_delete) == 'undefined') {
              data.visible_to_customer = $('#bulk_pf_visible_to_customer').prop('checked');
          } else {
              data.mass_delete = true;
          }

          var rows = $('.table-project-files').find('tbody tr');
          $.each(rows, function() {
              var checkbox = $($(this).find('td').eq(0)).find('input');
              if (checkbox.prop('checked') == true) {
                  ids.push(checkbox.val());
              }
          });

          data.ids = ids;
          $(e).addClass('disabled');

          setTimeout(function() {
              $.post(admin_url + 'casediary/bulk_action_files', data).done(function() {
                  window.location.reload();
              });

          }, 200);
      }

  }

  function gantt_filter() {
      var status = $('select[name="gantt_task_status"]').selectpicker('val');
      var gantt_type = $('select[name="gantt_type"]').selectpicker('val');
      var params = [];
      params['gantt_type'] = gantt_type;
      params['group'] = 'project_gantt';
      if (status) {
          params['gantt_task_status'] = status;
      }
      window.location.href = buildUrl(admin_url + 'casediary/view/' + project_id, params);
  }

  function confirm_project_status_change(e) {
      var data = {};
      $(e).attr('disabled', true);
      data.project_id = $(e).data('project-id');
      data.status_id = $(e).data('status-id');
      if (data.status_id == 4) {
          var $projectMarkedasFinishedInput = $('#project_marked_as_finished_email_to_contacts');
          if ($projectMarkedasFinishedInput.length > 0) {
              data.send_project_marked_as_finished_email_to_contacts = $projectMarkedasFinishedInput.prop('checked') === true ? 1 : 0;
          }
      }
      data.mark_all_tasks_as_completed = $('#mark_all_tasks_as_completed').prop('checked') === true ? 1 : 0;
      data.notify_project_members_status_change = $('#notify_project_members_status_change').prop('checked') === true ? 1 : 0;
      $.post(admin_url + 'casediary/mark_as', data).done(function(response) {
          response = JSON.parse(response);
          alert_float(response.success === true ? 'success' : 'warning', response.message);
          setTimeout(function() {
              window.location.reload();
          }, 1500);
      }).fail(function(data) {
          window.location.reload();
      });
  }

  function milestones_kanban_update(ui, object) {
      if (object === ui.item.parent()[0]) {
          data = {};
          data.order = [];
          data.milestone_id = $(ui.item.parent()[0]).parents('.milestone-column').data('col-status-id');
          data.task_id = $(ui.item).data('task-id');
          var tasks = $(ui.item.parent()[0]).parents('.milestone-column').find('.task');

          var i = 0;
          $.each(tasks, function() {
              data.order.push([$(this).data('task-id'), i]);
              i++;
          });
          check_kanban_empty_col('[data-task-id]');

          setTimeout(function() {
              $.post(admin_url + 'projects/update_task_milestone', data)
          }, 50);
      }
  }

  function milestones_kanban() {  
      init_kanban('casetemplates/milestones_kanban', milestones_kanban_update, '.project-milestone', 320, 360, after_milestones_kanban);
  }

  function after_milestones_kanban() {
      $("#kan-ban").sortable({
          helper: 'clone',
          item: '.kan-ban-col',
          cancel: '.milestone-not-sortable',
          update: function(event, ui) {
              var uncategorized_is_after = $(ui.item).next('ul.kan-ban-col[data-col-status-id="0"]');

              if (uncategorized_is_after.length) {
                  $(this).sortable('cancel');
                  return false;
              }

              var data = {}
              data.order = [];
              var status = $('.kan-ban-col');
              var i = 0;

              $.each(status, function() {
                  data.order.push([$(this).data('col-status-id'), i]);
                  i++;
              });

              $.post(admin_url + 'casetemplates/update_milestones_order', data);
          }
      });

      for (var i = -10; i < $('.task-phase').not('.color-not-auto-adjusted').length / 2; i++) {
          var r = 120;
          var g = 169;
          var b = 56;
          $('.task-phase:eq(' + (i + 10) + ')').not('.color-not-auto-adjusted').css('background', color(r - (i * 13), g - (i * 13), b - (i * 13))).css('border', '1px solid ' + color(r - (i * 12), g - (i * 12), b - (i * 12)));
      };
  }

  // When marking task as complete if the staff in on project milestones area, remove this task from milestone in case exists
  function _maybe_remove_task_from_project_milestone(task_id) {
      var $milestonesTasksWrappers = $('.milestone-column');
      if ($("body").hasClass('project') && $milestonesTasksWrappers.length > 0) {
          if ($('#exclude_completed_tasks').prop('checked') == true) {
              $milestonesTasksWrappers.find('[data-task-id="' + task_id + '"]').remove();
          }
      }
  }


  if ($('#hearing-files-upload').length > 0) {
          new Dropzone('#hearing-files-upload', $.extend({}, _dropzone_defaults(), {
              paramName: "file",
              uploadMultiple: true,
              parallelUploads: 20,
              maxFiles: 20,
              accept: function(file, done) {
                  done();
              },
              success: function(file, response) {
                  if (this.getUploadingFiles().length === 0 && this.getQueuedFiles().length === 0) {
                      window.location.href = admin_url + 'casediary/view/' + project_id + '?group=project_hearings';
                  }
              },
              sending: function(file, xhr, formData) {
                  //formData.append("visible_to_customer", $('input[name="visible_to_customer"]').prop('checked'));
              }
          }));
      }

     
$("body").on('click', '.new-case-task-to-milestone', function(e) { 
        e.preventDefault();
        var milestone_id = $(this).parents('.milestone-column').data('col-status-id');
        new_task(admin_url + 'tasks/task?rel_type=casediary&rel_id=' + project_id + '&milestone_id=' + milestone_id);
        $('body [data-toggle="popover"]').popover('hide');
    });
      

// Create new task directly from relation, related options selected after modal is shown
function new_template_task_from_relation(table, rel_type, rel_id) {
    if (typeof(rel_type) == 'undefined' && typeof(rel_id) == 'undefined') {
        rel_id = $(table).data('new-rel-id');
        rel_type = $(table).data('new-rel-type');
    }
    var url = admin_url + 'casetemplates/task_temp?rel_id=' + rel_id + '&rel_type=' + rel_type;
    new_temp_task(url);
}

// Initing relation tasks tables
function init_rel_temp_tasks_table(rel_id, rel_type, selector) {
    if (typeof(selector) == 'undefined') { selector = '.table-rel-tasks'; }
    var $selector = $("body").find(selector);
    if ($selector.length == 0) { return; }

    var TasksServerParams = {},
        not_sortable_tasks, Tasks_Filters;

    Tasks_Filters = $('._hidden_inputs._filters._tasks_filters input');

    $.each(Tasks_Filters, function() {
        TasksServerParams[$(this).attr('name')] = '[name="' + $(this).attr('name') + '"]';
    });

    var url = admin_url + 'casetemplates/init_relation_temp_tasks/' + rel_id + '/' + rel_type;

    not_sortable_tasks = [($selector.find('th').length - 1)];

    /*if ($selector.attr('data-new-rel-type') == 'project') {
        not_sortable_tasks.push(0);
        url += '?bulk_actions=true';
    }
    if ($selector.attr('data-new-rel-type') == 'casediary') {
        not_sortable_tasks.push(0);
        url += '?bulk_actions=true';
    }*/


    var Api = initDataTable($selector, url, not_sortable_tasks, not_sortable_tasks, TasksServerParams, [$selector.find('th.duedate').index(), 'ASC']);
    // Hide option on lead modal because the table reserved area is too small
    if (Api && rel_type == 'lead') {
        Api.column($('table .table-tasks-options').index()).visible(false, false).columns.adjust();
    }
}

// Go to edit view
function edit_task_temp(task_id) {
    requestGet('casetemplates/task_temp/' + task_id).done(function(response) {
        $('#_task').html(response)
        $('#task-modal').modal('hide');
        $("body").find('#_task_modal').modal({ show: true, backdrop: 'static' });
    });
}

// Init task modal and get data from server
function init_template_task_modal(task_id) {

    var queryStr = '';
    var $leadModal = $('#lead-modal');
    var $taskAddEditModal = $('#_task_modal');
    if ($leadModal.is(':visible')) {
        queryStr += '?opened_from_lead_id=' + $leadModal.find('input[name="leadid"]').val();
        $leadModal.modal('hide');
    } else if ($taskAddEditModal.attr('data-lead-id') != undefined) {
        queryStr += '?opened_from_lead_id=' + $taskAddEditModal.attr('data-lead-id');
    }

    requestGet('tasks/get_template_task_data/' + task_id + queryStr).done(function(response) {
        _task_template_append_html(response);
    }).fail(function(data) {
        $('#task-modal').modal('hide');
        alert_float('danger', data.responseText);
    });
}


// General function to append task html returned from request
function _task_template_append_html(html) { 
    $('#task-modal .data').html(html);
    //init_tasks_temp_checklist_items(false, task_id);
    recalculate_checklist_items_progress();
    do_task_checklist_items_height();
    setTimeout(function() {
        $('#task-modal').modal('show');
        // Init_tags_input is trigged too when task modal is shown
        // This line prevents triggering twice.
        if ($('#task-modal').is(':visible')) { init_tags_inputs(); }
        fix_task_modal_left_col_height();
    }, 150);
}


// New task function, various actions performed
function new_temp_task(url) { 
    url = typeof(url) != 'undefined' ? url : admin_url + 'casetemplates/task_temp';

    var $leadModal = $('#lead-modal');
    if ($leadModal.is(':visible')) {
        url += '&opened_from_lead_id=' + $leadModal.find('input[name="leadid"]').val();
        if (url.indexOf('?') === -1) { url = url.replace('&', '?'); }
        $leadModal.modal('hide');
    }

    var $taskSingleModal = $('#task-modal');
    if ($taskSingleModal.is(':visible')) { $taskSingleModal.modal('hide'); }

    var $taskEditModal = $('#_task_modal');
    if ($taskEditModal.is(':visible')) { $taskEditModal.modal('hide'); }

    requestGet(url).done(function(response) {
        $('#_task').html(response);
        $("body").find('#_task_modal').modal({ show: true, backdrop: 'static' });
    });
}


// Handles task add/edit form modal.
function temp_task_form_handler(form) {
    tinymce.triggerSave();
    $('#_task_modal').find('input[name="startdate"]').prop('disabled', false);

    var formURL = form.action;
    var formData = new FormData($(form)[0]);
 
    $.ajax({
        type: $(form).attr('method'),
        data: formData,
        mimeType: $(form).attr('enctype'),
        contentType: false,
        cache: false,
        processData: false,
        url: formURL
    }).done(function(response) {
        response = JSON.parse(response);
        if (response.success == true) { alert_float('success', response.message); }
        if (!$("body").hasClass('project')) {
            $('#_task_modal').attr('data-task-created', true);
            $('#_task_modal').modal('hid e'); 
            init_template_task_modal(response.id);
            reload_tasks_tables();
        } else { 
            // reload page on project area 
            var location = window.location.href;
            var params = [];
            location = location.split('?');
            var group = get_url_param('group');
            var excludeCompletedTasks = get_url_param('exclude_completed');
            if (group) { params['group'] = group; }
            /*if (excludeCompletedTasks) { params['exclude_completed'] = excludeCompletedTasks; }
            params['taskid'] = response.id;*/
            window.location.href = buildUrl(location[0], params)
        }
    }).fail(function(error) {
        alert_float('danger', JSON.parse(error.responseText));
    });

    return false;
}

// Change task priority from sigle modal
function temp_task_change_priority(priority_id, task_id) {
    url = 'tasks/change_temp_priority/' + priority_id + '/' + task_id;
    var taskModalVisible = $('#task-modal').is(':visible');
    url += '?single_task=' + taskModalVisible;
    requestGetJSON(url).done(function(response) {
        if (response.success == true) {
            reload_tasks_tables();
            if (taskModalVisible) { _task_template_append_html(response.taskHtml); }
        }
    });
}

// Update tags in task single modal
function temp_task_single_update_tags() {
    var taskTags = $("#taskTags");
    $.post(admin_url + 'tasks/update_tags_temp', {
        tags: taskTags.tagit('assignedTags'),
        task_id: taskTags.attr('data-taskid')
    });
}


$("body").on('blur', '#task-modal ul.tagit li.tagit-new input', function() {
        setTimeout(function() { temp_task_single_update_tags(); }, 100);
});

// New task checklist item
function add_temp_task_checklist_item(task_id, description) {
    description = typeof(description) == 'undefined' ? '' : description;
    
    $.post(admin_url + 'tasks/add_temp_checklist_item', {
        taskid: task_id,
        description: description
    }).done(function() {
        init_tasks_temp_checklist_items(true, task_id);
    });
}

 // Fetches task checklist items.
function init_tasks_temp_checklist_items(is_new, task_id) {
    $.post(admin_url + 'tasks/init_temp_checklist_items', {
        taskid: task_id
    }).done(function(data) {
        $('#checklist-items').html(data);
        if (typeof(is_new) != 'undefined') {
            var first = $('#checklist-items').find('.checklist textarea').eq(0);
            if (first.val() == '') { first.focus(); }
        }
        recalculate_checklist_items_progress();
        update_temp_checklist_order();
    });
}

// Updates task checklist items order
function update_temp_checklist_order() {
    var order = [];
    var items = $("body").find('.checklist');
    if (items.length == 0) { return; }
    var i = 1;
    $.each(items, function() {
        order.push([$(this).data('checklist-id'), i]);
        i++;
    });
    var data = {}
    data.order = order;
    $.post(admin_url + 'tasks/update_temp_checklist_order', data);
}

// Task single edit description with inline editor, used from task single modal
function edit_temp_task_inline_description(e, id) {

    tinyMCE.remove('#task_view_description');

    if ($(e).hasClass('editor-initiated')) {
        $(e).removeClass('editor-initiated');
        return;
    }

    $(e).addClass('editor-initiated');
    $.Shortcuts.stop();
    tinymce.init({
        selector: '#task_view_description',
        theme: 'inlite',
        skin: 'perfex',
        auto_focus: "task_view_description",
        plugins: 'table link paste contextmenu textpattern',
        insert_toolbar: 'quicktable',
        selection_toolbar: 'bold italic | quicklink h2 h3 blockquote',
        inline: true,
        table_default_styles: {
            width: '100%'
        },
        setup: function(editor) {
            editor.on('blur', function(e) {
                if (editor.isDirty()) {
                    $.post(admin_url + 'tasks/update_temp_task_description/' + id, {
                        description: editor.getContent()
                    });
                }
                setTimeout(function() {
                    editor.remove();
                    $.Shortcuts.start();
                }, 500);
            });
        }
    });
}

// Remove task checklist item from the task
function delete_temp_checklist_item(id, field) {
    requestGetJSON('tasks/delete_temp_checklist_item/' + id).done(function(response) {
        if (response.success == true) {
            $(field).parents('.checklist').remove();
            recalculate_checklist_items_progress();
        }
    });
}


 /* Insert new checklist items on enter press */
    $("body").on('keypress', 'textarea[name="checklist-description2"]', function(event) {
        if (event.which == '13') {
            $(this).focusout();
            add_temp_task_checklist_item($(this).attr('data-taskid'));
            return false;
        }
    });

    /* Update tasks checklist items when focusing out */
    $("body").on('blur paste', 'textarea[name="checklist-description2"]', function() {
        var textArea = $(this);
        var description = textArea.val();
        description = description.trim();
        var listid = textArea.parents('.checklist').data('checklist-id');

        $.post(admin_url + 'tasks/update_temp_checklist_item', {
            description: description,
            listid: listid
        }).done(function(response) {
            response = JSON.parse(response);
            if (response.can_be_template === true) {
                textArea.parents('.checklist').find('.save-checklist-template').removeClass('hide');
            }
            if (description === '') {
                $('#checklist-items').find('.checklist[data-checklist-id="' + listid + '"]').remove();
            }
        });
    }); 