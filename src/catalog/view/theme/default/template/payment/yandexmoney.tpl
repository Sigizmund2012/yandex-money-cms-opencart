<?php if (isset($header)) :
    echo $header;
endif; ?>
    <form accept-charset="UTF-8" enctype="application/x-www-form-urlencoded" method="POST" id='YamoneyForm' action="<?php echo $action; ?>">
    <?php if ($mode != 3):
        if (!$epl): ?>
            <h3><?php echo $method_label; ?></h3>
            <table class="radio">
                <tbody>
                <?php foreach ($allow_methods as $m_val => $m_name):
                    if ($org_mode || in_array($m_val, array('AC','PC'))):
                        $checked = ($default_method == $m_val)?'checked':'';
                        ?>
                        <tr class="highlight">
                            <td>
                                <label for="ym_<?php echo $m_val; ?>">
                                    <input type="radio" name="paymentType" value="<?php echo $m_val.'" '.$checked; ?> id="ym_<?php echo $m_val; ?>">
                                    <img src="<?php echo $imageurl.'yandexmoney/'.strtolower ($m_val).'.png'; ?>"/>
                                    <?php echo $m_name; ?>
                                </label>
                            </td>
                        </tr>
                    <?php endif;
                endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <input type="hidden" name="paymentType" value="">
        <?php endif;

        if ($org_mode): ?>
            <input type="hidden" name="shopid" value="<?php echo $shop_id;?>">
            <input type="hidden" name="scid" value="<?php echo $scid;?>">
            <input type="hidden" name="orderNumber" value="<?php echo $order_id;?>">
            <input type="hidden" name="sum" value="<?php echo $sum;?>" data-type="number" >
            <input type="hidden" name="customerNumber" value="<?php echo $customerNumber; ?>" >
            <input type="hidden" name="shopSuccessURL" value="<?php echo $shopSuccessURL; ?>" >
            <input type="hidden" name="shopFailURL" value="<?php echo $shopFailURL; ?>" >
            <?php if (isset($phone)): ?> <input type="hidden" name="cps_phone" value="<?php echo $phone;?>"> <?php endif; ?>
            <?php if (isset($email)): ?> <input type="hidden" name="cps_email" value="<?php echo $email;?>"> <?php endif; ?>
            <?php if (isset($receipt)): ?>
                <textarea name="ym_merchant_receipt" style="display:none;"><?php echo $receipt; ?></textarea>
            <?php endif; ?>
            <input type="hidden" name="cms_name" value="<?php echo $cmsname; ?>" >
        <?php else: ?>
            <input type="hidden" name="receiver" value="<?php echo $account; ?>">
            <input type="hidden" name="formcomment" value="<?php echo $formcomment;?>">
            <input type="hidden" name="short-dest" value="<?php echo $short_dest;?>">
            <input type="hidden" name="writable-targets" value="false">
            <input type="hidden" name="comment-needed" value="true">
            <input type="hidden" name="label" value="<?php echo $order_id;?>">
            <input type="hidden" name="successURL" value="<?php echo $shopSuccessURL; ?>" >
            <input type="hidden" name="quickpay-form" value="shop">
            <input type="hidden" name="targets" value="<?php echo $order_text;?> <?php echo $order_id;?>">
            <input type="hidden" name="sum" value="<?php echo $sum;?>" data-type="number" >
            <input type="hidden" name="comment" value="<?php echo $comment; ?>" >
            <input type="hidden" name="need-fio" value="true">
            <input type="hidden" name="need-email" value="true" >
            <input type="hidden" name="need-phone" value="false">
            <input type="hidden" name="need-address" value="false">
        <?php endif; ?>
    <?php else : ?>
        <input type="hidden" name="formId" value="<?php echo $formId; ?>" />
        <input type="hidden" name="narrative" value="<?php echo $narrative; ?>" />
        <div style="padding-bottom: 20px;">
            <label for="ya-fio">ФИО плательщика</label>
            <input type="text" name="fio" id="ya-fio" value="<?php echo $fio; ?>" />
            <div id="ya-fio-error"></div>
        </div>
        <input type="hidden" name="sum" value="<?php echo $sum; ?>" data-type="number" >
        <input type="hidden" name="cms_name" value="opencart15" data-type="number" >
        <input type="hidden" name="quickPayVersion" value="2" >
    <?php endif; ?>

        <div class="buttons">
            <div class="right">
                <a href="javascript://" id="button-confirm" name="submit-button" class="button"><?php echo $button_confirm; ?></a>
            </div>
        </div>
    </form>

<?php if ($mode != 3): ?>
    <script type="text/javascript"><!--
        $('#button-confirm').bind('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            $.ajax({
                type: 'get',
                url: 'index.php?route=payment/yandexmoney/confirm'
            });
            $("#YamoneyForm").submit();
        });
        $('input[name=paymentType]').bind('click', function() {
            if ($('input[name=paymentType]:checked').val()=='MP'){
                var textMpos='<?php echo $mpos_page_url; ?>';
                $("#YamoneyForm").attr('action', textMpos.replace(/&amp;/g, '&'));

            }else{
                $("#YamoneyForm").attr('action', '<?php echo $action; ?>');
            }
        });
        //--></script>
<?php else: ?>
    <script type="text/javascript"><!--
        $('#button-confirm').bind('click', function(e) {
            var field = $('#ya-fio');
            var parts = field.val().trim().split(/\s+/);
            if (parts.length == 3) {
                $("#ya-fio-error").text("");
                $("#YamoneyForm").submit();
            } else {
                $("#ya-fio-error").text("Укажите фамилию, имя и отчество плательщика");
            }
        });
    //--></script>
<?php endif; ?>
<?php if (isset($footer)):
    echo $footer;
endif; ?>