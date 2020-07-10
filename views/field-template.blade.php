<td style="vertical-align: middle">
    <input type="text" style="width: 100%" required class="form-control txtFieldName"/>
    <input type="text" class="form-control foreignTable txtForeignTable" style="display: none"
           placeholder="Foreign table,Primary key"/>
</td>
<td style="vertical-align: middle">
    <select class="form-control txtdbType" style="width: 100%">
        <option value="increments">Increments</option>
        <option value="integer">Integer</option>
        <option value="smallInteger">SmallInteger</option>
        <option value="longText">LongText</option>
        <option value="bigInteger">BigInteger</option>
        <option value="double">Double</option>
        <option value="float">Float</option>
        <option value="decimal">Decimal</option>
        <option value="boolean">Boolean</option>
        <option value="string">String</option>
        <option value="char">Char</option>
        <option value="text">Text</option>
        <option value="mediumText">MediumText</option>
        <option value="longText">LongText</option>
        <option value="enum">Enum</option>
        <option value="binary">Binary</option>
        <option value="dateTime">DateTime</option>
        <option value="date">Date</option>
        <option value="timestamp">Timestamp</option>
    </select>

    <input type="text" class="form-control dbValue txtDbValue" style="display: none"
           placeholder=""/>
</td>
<td style="vertical-align: middle">
    <input type="text" class="form-control txtValidation"/>
</td>
<td style="vertical-align: middle" width="200">
    <select class="form-control drdHtmlType" style="width: 100%">
        <option value="text">Text</option>
        <option value="image">Image</option>
        <option value="select">Select</option>
        <option value="select_mul">Select Multible</option>
        <option value="option">Option</option>
        <option value="date">Date</option>
        <option value="datetime">Datetime</option>
        <option value="password">Password</option>
        <option value="radio">Radio</option>
        <option value="checkbox">Checkbox</option>
        <option value="textarea">TextArea</option>
        <option value="editor">Editor</option>
        <option value="toggle-switch">Toggle</option>
    </select>
    <textarea type="text" class="form-control htmlValue txtHtmlValue" style="display: none"
           placeholder="" rows="2"></textarea>
</td>
<td style="vertical-align: middle" width="100">
    <select class="form-control drdHtmlCol" style="width: 100%">
        <option value="col-1">col-1</option>
        <option value="col-2">col-2</option>
        <option value="col-3">col-3</option>
        <option value="col-4">col-4</option>
        <option value="col-5">col-5</option>
        <option value="col-6">col-6</option>
        <option value="col-7">col-7</option>
        <option value="col-8">col-8</option>
        <option value="col-9">col-9</option>
        <option value="col-10">col-10</option>
        <option value="col-11">col-11</option>
        <option value="col-12">col-12</option>
    </select>
    <textarea type="text" class="form-control htmlValue txtHtmlValue" style="display: none"
           placeholder="" rows="2"></textarea>
</td>
<td style="vertical-align: middle">
    <div class="checkbox" style="text-align: center">
        <label style="padding-left: 0px">
            <input type="checkbox" style="margin-left: 0px!important;" class="flat-red chkPrimary"/>
        </label>
    </div><br>
    <select class="form-control drdPosition" style="width: 100%">
        <option value="">Default</option>
        <option value="column_left">Left</option>
        <option value="column_right">Right</option>
    </select>
</td>
<td style="vertical-align: middle">
    <div class="checkbox" style="text-align: center">
        <label style="padding-left: 0px">
            <input type="checkbox" style="margin-left: 0px!important;" class="flat-red chkForeign"/>
        </label>
    </div>
</td>
<td style="vertical-align: middle">
    <div class="checkbox" style="text-align: center">
        <label style="padding-left: 0px">
            <input type="checkbox" style="margin-left: 0px!important;" class="flat-red chkSearchable" checked/>
        </label>
    </div>
</td>
<td style="vertical-align: middle">
    <div class="checkbox" style="text-align: center">
        <label style="padding-left: 0px">
            <input type="checkbox" style="margin-left: 0px!important;" class="flat-red chkFillable" checked/>
        </label>
    </div>
</td>
<td style="vertical-align: middle">
    <div class="checkbox" style="text-align: center">
        <label style="padding-left: 0px">
            <input type="checkbox" style="margin-left: 0px!important;" class="flat-red chkInForm" checked/>
        </label>
    </div>
</td>
<td style="vertical-align: middle">
    <div class="checkbox" style="text-align: center">
        <label style="padding-left: 0px">
            <input type="checkbox" style="margin-left: 0px!important;" class="flat-red chkInIndex" checked/>
        </label>
    </div>
</td>
<td style="text-align: center;vertical-align: middle">
    <i onclick="removeItem(this)" class="remove fa fa-trash-o"
       style="cursor: pointer;font-size: 20px;color: red"></i>
</td>
