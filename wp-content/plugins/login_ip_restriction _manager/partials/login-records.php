<style>
    .login-records-filter input[type="search"] {
        min-width: 409px;
    }

    .login-records-filter form {
        gap: 16px;
    }

    .login-records-filter .date-field {
        min-width: 416px;
    }

    .login-records-filter input[type="search"]:focus,
    .login-records-filter input[type="date"]:focus {
        background-color: #2b2937 !important;
    }

    .table-head-col:nth-child(1), .table-body-col:nth-child(1) {
        width: 37.888%;
    }
    
    .table-head-col:nth-child(2), .table-body-col:nth-child(2) {
        width: 30.31%;
    }

    .table-head-col:nth-child(3) {
        text-align: left;
    }
    
    .table-head-col:nth-child(3), .table-body-col:nth-child(3) {
        width: 31.7%;
    }

    @media (max-width: 1760px) {
        .login-records-table {
            width: 100%;
        }
    }

    @media (max-width: 1735px) and (min-width: 768px) {
        .login-records-filter .date-field {
            min-width: clamp(18.75rem, 16.315vw + 8.308rem, 26rem);
        }

        .login-records-filter input[type="search"] {
            min-width: clamp(14.375rem, 25.176vw + -1.738rem, 25.563rem);
        }

        .login-records-filter form {
            flex-wrap: nowrap;
        }
    }

</style>

<div class="login-records-template">
    <div class="template-title">
        <h1>Login Records</h1>
    </div>
    <div class="login-records-filter filter-area">
        <form action="#">
            <div class="filter-search-field">
                <input type="search" class="cuim-login-records-search-validation-254" maxlength="254" name="login-records-search" id="login-records-search" placeholder="Search By Account">
            </div>
            <div class="login-records-filter-date">
                <div class="filter-select date-field">
                    <input type="text" name="daterange" class="select-date-picker" value="" id="daterange"
                        placeholder="YYYY/MM/DD 00:00 - YYYY/MM/DD 00:00">
                    <span class="calendar-icon"></span>
                </div>
            </div>
            <button type="submit" class="filter-select-button" id="agqa-login-records-filters"><span>Search</span></button>
        </form>
    </div>
    <div class="custom-table-ctn">
        <div class="custom-table-ctn-inner">
            <div class="login-records-table custom-table">
                <div class="custom-table-head">
                    <div class="table-head-col">Account</div>
                    <div class="table-head-col"> Login Time</div>
                    <div class="table-head-col">Login IP Address</div>
                </div>
                <div class="custom-table-body">
                    <div class="custom-table-row">
                        <div class="table-body-col">johnsonjoshua</div>
                        <div class="table-body-col">2026/11/12 12:02</div>
                        <div class="table-body-col">111.222.33</div>
                    </div>
                    <div class="custom-table-row">
                        <div class="table-body-col">johnsonjoshua</div>
                        <div class="table-body-col">2026/11/12 12:02</div>
                        <div class="table-body-col">111.222.33</div>
                    </div>
                    <div class="custom-table-row">
                        <div class="table-body-col">johnsonjoshua</div>
                        <div class="table-body-col">2026/11/12 12:02</div>
                        <div class="table-body-col">111.222.33</div>
                    </div>
                    <div class="custom-table-row">
                        <div class="table-body-col">johnsonjoshua</div>
                        <div class="table-body-col">2026/11/12 12:02</div>
                        <div class="table-body-col">111.222.33</div>
                    </div>
                    <div class="custom-table-row">
                        <div class="table-body-col">johnsonjoshua</div>
                        <div class="table-body-col">2026/11/12 12:02</div>
                        <div class="table-body-col">111.222.33</div>
                    </div>
                    <div class="custom-table-row">
                        <div class="table-body-col">johnsonjoshua</div>
                        <div class="table-body-col">2026/11/12 12:02</div>
                        <div class="table-body-col">111.222.33</div>
                    </div>
                    <div class="custom-table-row">
                        <div class="table-body-col">johnsonjoshua</div>
                        <div class="table-body-col">2026/11/12 12:02</div>
                        <div class="table-body-col">111.222.33</div>
                    </div>
                    <div class="custom-table-row">
                        <div class="table-body-col">johnsonjoshua</div>
                        <div class="table-body-col">2026/11/12 12:02</div>
                        <div class="table-body-col">111.222.33</div>
                    </div>
                    <div class="custom-table-row">
                        <div class="table-body-col">johnsonjoshua</div>
                        <div class="table-body-col">2026/11/12 12:02</div>
                        <div class="table-body-col">111.222.33</div>
                    </div>
                    <div class="custom-table-row">
                        <div class="table-body-col">johnsonjoshua</div>
                        <div class="table-body-col">2026/11/12 12:02</div>
                        <div class="table-body-col">111.222.33</div>
                    </div>
                    <div class="custom-table-row">
                        <div class="table-body-col">johnsonjoshua</div>
                        <div class="table-body-col">2026/11/12 12:02</div>
                        <div class="table-body-col">111.222.33</div>
                    </div>
                    <div class="custom-table-row">
                        <div class="table-body-col">johnsonjoshua</div>
                        <div class="table-body-col">2026/11/12 12:02</div>
                        <div class="table-body-col">111.222.33</div>
                    </div>
                    <div class="custom-table-row">
                        <div class="table-body-col">johnsonjoshua</div>
                        <div class="table-body-col">2026/11/12 12:02</div>
                        <div class="table-body-col">111.222.33</div>
                    </div>
                    <div class="custom-table-row">
                        <div class="table-body-col">johnsonjoshua</div>
                        <div class="table-body-col">2026/11/12 12:02</div>
                        <div class="table-body-col">111.222.33</div>
                    </div>
                    <div class="custom-table-row">
                        <div class="table-body-col">johnsonjoshua</div>
                        <div class="table-body-col">2026/11/12 12:02</div>
                        <div class="table-body-col">111.222.33</div>
                    </div>
                    <div class="custom-table-row">
                        <div class="table-body-col">johnsonjoshua</div>
                        <div class="table-body-col">2026/11/12 12:02</div>
                        <div class="table-body-col">111.222.33</div>
                    </div>
                    <div class="custom-table-row">
                        <div class="table-body-col">johnsonjoshua</div>
                        <div class="table-body-col">2026/11/12 12:02</div>
                        <div class="table-body-col">111.222.33</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>