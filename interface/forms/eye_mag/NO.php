<!-- start NO -->
<div id="POSTSEG_1" class="clear_both">
    <div id="NERVIO_left" name="NERVIO_left" class="exam_section_left borderShadow">
        <span class="anchor" id="NERVIO_anchor"></span>
        <div class="TEXT_class" id="NERVIO_left_text" name="NERVIO_left_text">
                                    <span class="closeButton_2 fa fa-paint-brush"
                                          title="<?php echo xla('Open/Close the NERVIO drawing panel'); ?>"
                                          id="BUTTON_DRAW_NERVIO" name="BUTTON_DRAW_NERVIO"></span>
            <i class="closeButton_3 fa fa-database"
               title="<?php echo xla('Open/Close the NERVIO Exam Quick Picks panel'); ?>"
               id="BUTTON_QP_NERVIO" name="BUTTON_QP_NERVIO"></i>
            <i class="closeButton_4 fa fa-user-md fa-sm fa-2" name="Shorthand_kb"
               title="<?php echo xla("Open/Close the Shorthand Window and display Shorthand Codes"); ?>"></i>
            <i class="closeButton fa fa-minus-circle"
               title="<?php echo xla('Open/Close Post Seg panels'); ?>" id="BUTTON_TAB_POSTSEG"
               name="BUTTON_TAB_POSTSEG"></i>
            <b><?php echo xlt('Nervio Optico'); ?>:</b>
            <div class="kb kb_left"
                 title="<?php echo xla("Nervio Default Values"); ?>"><?php echo text('DRET'); ?></div>
            <br/>
            <div id="RETINA_left_1" class="text_clinical">

                <table>
                    <tr class="bold">
                        <td></td>
                        <td><?php echo xlt('OD{{right eye}}'); ?> </td>
                        <td><?php echo xlt('OS{{left eye}}'); ?> </td>
                    </tr>
                    <tr>

                        <td class="bold right">
                            <div class="kb kb_left"><?php echo 'CUP'; ?></div>
                            <?php echo xlt('C/D Ratio{{cup to disc ration}}'); ?>:
                        </td>
                        <td>
                            <input type="text" class="NERVIO" name="ODCOPA" size="4" id="ODCOPA"
                                   value="<?php echo attr($ODCUP); ?>">
                        </td>
                        <td>
                            <input type="text" class="NERVIO" name="OSCOPA" size="4" id="OSCOPA"
                                   value="<?php echo attr($OSCUP); ?>">
                        </td>
                    </tr>
                </table>
                <br/>
                <table>
                    <?php
                    list($imaging, $episode) = display($pid, $encounter, "NEURO");
                    echo $episode;
                    ?>
                </table>
            </div>

            <?php ($NERVIO_VIEW == 1) ? ($display_NERVIO_view = "wide_textarea") : ($display_NERVIO_view = "narrow_textarea"); ?>
            <?php ($display_NERVIO_view == "wide_textarea") ? ($marker = "fa-minus-square-o") : ($marker = "fa-plus-square-o"); ?>
            <div>
                <div id="NERVIO_text_list" name="NERVIO_text_list"
                     class="borderShadow  <?php echo attr($display_NERVIO_view); ?>">
                                            <span class="top_right fa <?php echo attr($marker); ?>"
                                                  name="NERVIO_text_view" id="NERVIO_text_view"></span>
                    <table cellspacing="0" cellpadding="0">
                        <tr>
                            <th><?php echo xlt('OD{{right eye}}'); ?></th>
                            <th></th>
                            <th><?php echo xlt('OS{{left eye}}'); ?></th>
                            </td>
                        </tr>
                        <tr>
                            <td><textarea name="ODDISC" id="ODDISC"
                                          class="NERVIO right"><?php echo text($ODDISC); ?></textarea>
                            </td>
                            <td>
                                <div class="ident"><?php echo xlt('Disc'); ?></div>
                                <div class="kb kb_left"><?php echo xlt('RD{{right disc}}'); ?></div>
                                <div class="kb kb_right"><?php echo xlt('LD{{left disc}}'); ?></div>
                            </td>
                            <td><textarea name="OSDISC" id="OSDISC"
                                          class="NERVIO"><?php echo text($OSDISC); ?></textarea>
                            </td>
                        </tr>
                        <tr>
                            <td><textarea name="ODDISC" id="ODDISC"
                                          class="NERVIO right"><?php echo text($ODDISC); ?></textarea>
                            </td>
                            <td>
                                <div class="ident"><?php echo xlt('Vessels'); ?></div>
                                <div class="kb kb_left"><?php echo xlt('RD{{right disc}}'); ?></div>
                                <div class="kb kb_right"><?php echo xlt('LD{{left disc}}'); ?></div>
                            </td>
                            <td><textarea name="OSDISC" id="OSDISC"
                                          class="NERVIO"><?php echo text($OSDISC); ?></textarea>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
            <div class="QP_lengthen" id="NERVIO_COMMENTS_DIV">
                <b><?php echo xlt('Comments'); ?>:</b>
                <div class="kb kb_left"><?php echo xlt('RCOM{{right comments}}'); ?></div>
                <br/>
                <textarea id="NERVIO_COMMENTS" class="NERVIO"
                          name="NERVIO_COMMENTS"><?php echo text($NERVIO_COMMENTS); ?></textarea>
            </div>
        </div>
    </div>

    <div id="NERVIO_right" class="exam_section_right borderShadow text_clinical">
        <div id="PRIORS_NERVIO_left_text"
             name="PRIORS_NERVIO_left_text"
             class="PRIORS_class PRIORS"><i class="fa fa-spinner fa-spin"></i>
        </div>
        <?php display_draw_section("NERVIO", $encounter, $pid); ?>
        <div id="QP_NERVIO" name="QP_NERVIO" class="QP_class">
            <input type="hidden" id="NERVIO_prefix" name="NERVIO_prefix" value=""/>
            <div class="qp10">
                                        <span class="eye_button  eye_button_selected" id="NERVIO_prefix_off"
                                              name="NERVIO_prefix_off"
                                              onclick="$('#NERVIO_prefix').val('').trigger('change');"><?php echo xlt('Off'); ?></span>
                <span class="eye_button" id="NERVIO_defaults"
                      name="NERVIO_defaults"><?php echo xlt('Defaults'); ?></span>
                <span class="eye_button" id="NERVIO_prefix_no" name="NERVIO_prefix_no"
                      onclick="$('#NERVIO_prefix').val('no').trigger('change');"> <?php echo xlt('no'); ?> </span>
                <span class="eye_button" id="NERVIO_prefix_trace" name="NERVIO_prefix_trace"
                      onclick="$('#NERVIO_prefix').val('trace').trigger('change');"> <?php echo xlt('tr'); ?> </span>
                <span class="eye_button" id="NERVIO_prefix_1" name="NERVIO_prefix_1"
                      onclick="$('#NERVIO_prefix').val('+1').trigger('change');"> <?php echo xlt('+1'); ?> </span>
                <span class="eye_button" id="NERVIO_prefix_2" name="NERVIO_prefix_2"
                      onclick="$('#NERVIO_prefix').val('+2').trigger('change');"> <?php echo xlt('+2'); ?> </span>
                <span class="eye_button" id="NERVIO_prefix_3" name="NERVIO_prefix_3"
                      onclick="$('#NERVIO_prefix').val('+3').trigger('change');"> <?php echo xlt('+3'); ?> </span>
                <?php echo $selector = priors_select("NERVIO", $id, $id, $pid); ?>
            </div>
            <div name="QP_11">
                                        <span class="eye_button" id="NERVIO_prefix_1mm" name="NERVIO_prefix_1mm"
                                              onclick="$('#NERVIO_prefix').val('1mm').trigger('change');"> <?php echo xlt('1mm'); ?> </span>
                <br/>
                <span class="eye_button" id="NERVIO_prefix_2mm" name="NERVIO_prefix_2mm"
                      onclick="$('#NERVIO_prefix').val('2mm').trigger('change');"> <?php echo xlt('2mm'); ?> </span>
                <br/>
                <span class="eye_button" id="NERVIO_prefix_3mm" name="NERVIO_prefix_3mm"
                      onclick="$('#NERVIO_prefix').val('3mm').trigger('change');"> <?php echo xlt('3mm'); ?> </span>
                <br/>
                <span class="eye_button" id="NERVIO_prefix_4mm" name="NERVIO_prefix_4mm"
                      onclick="$('#NERVIO_prefix').val('4mm').trigger('change');"> <?php echo xlt('4mm'); ?> </span>
                <br/>
                <span class="eye_button" id="NERVIO_prefix_5mm" name="NERVIO_prefix_5mm"
                      onclick="$('#NERVIO_prefix').val('5mm').trigger('change');"> <?php echo xlt('5mm'); ?> </span>
                <br/>
                <span class="eye_button" id="NERVIO_prefix_nasal" name="NERVIO_prefix_nasal"
                      onclick="$('#NERVIO_prefix').val('nasal').trigger('change');"><?php echo xlt('nasal'); ?></span>
                <span class="eye_button" id="NERVIO_prefix_temp" name="NERVIO_prefix_temp"
                      onclick="$('#NERVIO_prefix').val('temp').trigger('change');"><?php echo xlt('temp{{temporal}}'); ?></span>
                <span class="eye_button" id="NERVIO_prefix_superior"
                      name="NERVIO_prefix_superior"
                      onclick="$('#NERVIO_prefix').val('superior').trigger('change');"><?php echo xlt('sup{{superior}}'); ?></span>
                <span class="eye_button" id="NERVIO_prefix_inferior"
                      name="NERVIO_prefix_inferior"
                      onclick="$('#NERVIO_prefix').val('inferior').trigger('change');"><?php echo xlt('inf{{inferior}}'); ?></span>
                <span class="eye_button" id="NERVIO_prefix_anterior"
                      name="NERVIO_prefix_anterior"
                      onclick="$('#NERVIO_prefix').val('anterior').trigger('change');"><?php echo xlt('ant{{anterior}}'); ?></span>
                <br/>
                <span class="eye_button" id="NERVIO_prefix_mid" name="NERVIO_prefix_mid"
                      onclick="$('#NERVIO_prefix').val('mid').trigger('change');"><?php echo xlt('mid{{middle}}'); ?></span>
                <br/>
                <span class="eye_button" id="NERVIO_prefix_posterior"
                      name="NERVIO_prefix_posterior"
                      onclick="$('#NERVIO_prefix').val('posterior').trigger('change');"><?php echo xlt('post{{posterior}}'); ?></span>
                <br/>
                <span class="eye_button" id="NERVIO_prefix_deep" name="NERVIO_prefix_deep"
                      onclick="$('#NERVIO_prefix').val('deep').trigger('change');"><?php echo xlt('deep'); ?></span>
                <br/>
                <br/>
                <span class="eye_button" id="NERVIO_prefix_clear" name="NERVIO_prefix_clear"
                      title="<?php echo xla('This will clear the data from all Nervio Exam fields'); ?>"
                      onclick="$('#NERVIO_prefix').val('clear').trigger('change');"><?php echo xlt('clear'); ?></span>
            </div>
            <div class="QP_block borderShadow text_clinical">
                <?php echo $QP_NERVIO = display_QP("NERVIO", $provider_id); ?>
            </div>
            <span class="closeButton fa fa-close pull-right z100" id="BUTTON_TEXTD_NERVIO"
                  name="BUTTON_TEXTD_NERVIO" value="1"></span>
        </div>
    </div>
</div>
<!-- end NO -->