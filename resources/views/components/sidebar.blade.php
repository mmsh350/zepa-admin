<ul class="main-menu">
    <!-- Start::slide -->
    <li class="slide">
        <a href="{{ route('dashboard') }}" class="side-menu__item @if ($menu == 'dashboard') active @endif">
            <i class="bx bx-home side-menu__icon"></i>
            <span class="side-menu__label">Dashboard</span>
        </a>
    </li>

    <li class="slide has-sub @if ($menu == 'wallet') open @endif">
        <a href="javascript:void(0);" class="side-menu__item @if ($menu == 'wallet') active @endif">
            <i class="bx bx-wallet side-menu__icon"></i> <box-icon type='solid' name='wallet'></box-icon>
            <span class="side-menu__label">Wallet</span>
            <i class="fe fe-chevron-right side-menu__angle"></i>
        </a>

        <ul class="slide-menu child1">
            <li class="slide">
                <a href="{{ route('funding') }}"
                    class="side-menu__item @if ($title == 'funding') active @endif">Funding</a>
            </li>
            <li class="slide">
                <a href="{{ route('p2p') }}"
                    class="side-menu__item @if ($title == 'p2p') active @endif">P2P
                </a>
            </li>
            <li class="slide">
                <a href="{{ route('claim') }}"
                    class="side-menu__item @if ($title == 'claim') active @endif">Claim Bonus
                </a>
            </li>
        </ul>
    </li>
    <!-- End::slide -->
    <!-- Start::slide -->
    <li class="slide has-sub @if ($menu == 'Identity') open @endif">
        <a href="javascript:void(0);" class="side-menu__item  @if ($menu == 'Identity') active @endif">
            <i class="bx bx-fingerprint side-menu__icon"></i>
            <span class="side-menu__label">Identity</span>
            <i class="fe fe-chevron-right side-menu__angle"></i>
        </a>
        <ul class="slide-menu child1">
            <li class="slide has-sub">
                <a href="javascript:void(0);" class="side-menu__item"> NIN Verification <i
                        class="fe fe-chevron-right side-menu__angle"></i> <span
                        class="badge bg-secondary-transparent ms-1">soon</span></a>
                <ul class="slide-menu child2">
                    <li class="slide">
                        <a href="#" onclick="return confirm('Comming Soon')" class="side-menu__item"> Verify NIN
                            using NIN</a>
                    </li>
                    <li class="slide">
                        <a href="#" onclick="return confirm('Comming Soon')" class="side-menu__item">Verify NIN
                            using Phone No
                        </a>
                    </li>
                    <li class="slide">
                        <a href="#" class="side-menu__item" onclick="return confirm('Comming Soon')">Verify Using
                            Demographic</a>
                    </li>
                    <li class="slide">
                        <a href="#" class="side-menu__item" onclick="return confirm('Comming Soon')">Verify VNIN
                        </a>
                    </li>
                </ul>
            </li>
            <li class="slide has-sub @if ($menu == 'Identity') open @endif">
                <a href="javascript:void(0);"
                    class="side-menu__item @if ($title == 'BVN') active @endif">BVN Verification
                    <i class="fe fe-chevron-right side-menu__angle"></i></a>
                <ul class="slide-menu child2">
                    <li class="slide">
                        <a href="{{ route('bvn') }}"
                            class="side-menu__item  @if ($title == 'BVN') active @endif">Verify BVN</a>
                    </li>
                </ul>
            </li>
            <li class="slide">
                <a href="{{ route('bank') }}"
                    class="side-menu__item @if ($title == 'BANK') active @endif">Verify Bank Account</a>
            </li>
        </ul>
    </li>
    <!-- End::slide -->
    <!-- Start::slide -->
    <li class="slide has-sub @if ($menu == 'Utility') open @endif">
        <a href="javascript:void(0);" class="side-menu__item @if ($title == 'Airtime') active @endif">
            <i class="bx bx-task side-menu__icon"></i>
            <span class="side-menu__label">Utilities</span>
            <i class="fe fe-chevron-right side-menu__angle"></i>
        </a>
        <ul class="slide-menu child1">
            <li class="slide">
                <a href="{{ route('airtime') }}"
                    class="side-menu__item @if ($title == 'airtime') active @endif">Airtime</a>
            </li>
            <li class="slide">

            <li class="slide has-sub @if ($title == 'data') open @endif">
                <a href="javascript:void(0);" class="side-menu__item"> Data Top-up<i
                        class="fe fe-chevron-right side-menu__angle"></i></a>
                <ul class="slide-menu child2">
                    <li class="slide">
                        <a href="{{ route('data') }}"
                            class="side-menu__item @if ($title == 'data') active @endif">Data Bundle</a>
                    </li>
                    <li class="slide">
                        <a href="#" onclick="return confirm('Comming Soon')" class="side-menu__item">SME Data
                            Bundle
                            <span class="badge bg-secondary-transparent ms-1">soon</span>
                        </a>
                    </li>
                </ul>
            </li>
    </li>
    <li class="slide">
        <a href="#" onclick="return confirm('Comming Soon')" class="side-menu__item">Cable Subscriptions
        </a>
    </li>
    <li class="slide">
        <a href="#" onclick="return confirm('Comming Soon')" class="side-menu__item">Electric Bills
        </a>
    </li>
    <li class="slide">
        <a href="#" onclick="return confirm('Comming Soon')" class="side-menu__item">Airtime to Cash
        </a>
    </li>
</ul>
</li>
<li class="slide">
    <a href="#" class="side-menu__item @if ($menu == 'dashboard1') active @endif">
        <i class="bx bx-user-pin side-menu__icon"></i>
        <span class="side-menu__label">Educational Pin</span>
    </a>
</li>
<!-- Start::slide -->
@if (Auth::user()->role != 'agent')
    <li class="slide has-sub @if ($menu == 'agency') open @endif">
        <a href="javascript:void(0);" class="side-menu__item @if ($menu == 'agency') active @endif">
            <i class="bx bx-user-plus side-menu__icon"></i>
            <span class="side-menu__label">Agent Services </span>
            <i class="fe fe-chevron-right side-menu__angle"></i>
        </a>
        <ul class="slide-menu child1">
            <li class="slide">
                <a href="{{ route('bvn-modification') }}"
                    class="side-menu__item  @if ($title == 'bvn-mod') active @endif">BVN Modification </a>
            </li>
            <li class="slide">
                <a href="{{ route('crm') }}"
                    class="side-menu__item @if ($title == 'crm') active @endif">CRM</a>
            </li>
            <li class="slide">
                <a href="#" onclick="return confirm('Comming Soon')" class="side-menu__item ">Account Upgrade
                </a>
            </li>
            <li class="slide">
                <a href="#" onclick="return confirm('Comming Soon')" class="side-menu__item">Agency Request
                </a>
            </li>
            <li class="slide">
                <a href="{{ route('crm2') }}"
                    class="side-menu__item @if ($title == 'crm2') active @endif">Find BVN using Phone and
                    DOB
                </a>
            </li>
            <li class="slide">
                <a href="#" onclick="return confirm('Comming Soon')" class="side-menu__item">BVN Enrollement
                    Agency Request
                </a>
            </li>
        </ul>
    </li>
@endif
<li class="slide has-sub @if ($menu == 'users') open @endif">
    <a href="javascript:void(0);" class="side-menu__item @if ($menu == 'users') active @endif">
        <i class="bx bx-user side-menu__icon"></i>
        <span class="side-menu__label">User Management </span>
        <i class="fe fe-chevron-right side-menu__angle"></i>
    </a>
    <ul class="slide-menu child1">
        <li class="slide">
            <a href="{{ route('verification.kyc') }}"
                class="side-menu__item @if ($title == 'kyc') active @endif">KYC Verification</a>
        </li>
        <li class="slide">
            <a href="#" onclick="return confirm('Comming Soon')" class="side-menu__item ">Manage Users
            </a>
        </li>
    </ul>
</li>
<li class="slide">
    <a href="{{ route('support') }}" target="_blank"
        class="side-menu__item @if ($menu == 'dashboard1') active @endif">
        <i class="bx bx-headphone side-menu__icon"></i>
        <span class="side-menu__label">Support</span>
    </a>
</li>

<li class="slide">
    <a href="{{ route('dashboard') }}" class="side-menu__item @if ($menu == 'dashboard1') active @endif">
        <i class="bx bx-cog side-menu__icon"></i>
        <span class="side-menu__label">Settings</span>
    </a>
</li>
<li class="slide">
    <a href="#" id="logout"
        onclick="logout();"class="side-menu__item @if ($menu == 'dashboard1') active @endif">
        <i class="bx bx-exit side-menu__icon"></i>
        <span class="side-menu__label">Logout</span>
    </a>
</li>
<!-- End::slide -->
</ul>
