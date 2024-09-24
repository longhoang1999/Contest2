{{--
    --- show
    Thông tin chung
    Test 1
        test11-navbar
        test12-navbar
--}}

@php
    use Illuminate\Support\Facades\DB;
    use Carbon\Carbon;
    use Cartalyst\Sentinel\Native\Facades\Sentinel;

    $notifications = DB::table('notification')
            ->where('userSend', '<>', Sentinel::getUser()->id)
            ->where('userReceiver', 'all')
            ->orderBy('create_at', 'desc')
            ->limit(7)
            ->get();
    if (!function_exists('customDiffTime')) {
        function customDiffTime($fromDateTime, $toDateTime = null){
            $toDateTime = $toDateTime ?: Carbon::now();
            $diff = $toDateTime->diff($fromDateTime);
            if ($diff->y > 0) {
                return $diff->y . ' year';
            } elseif ($diff->m > 0) {
                return $diff->m . ' mon';
            } elseif ($diff->d > 0) {
                return $diff->d . ' day';
            } elseif ($diff->h > 0) {
                return $diff->h . ' hr';
            } elseif ($diff->i > 0) {
                return $diff->i . ' min';
            } elseif ($diff->s > 0) {
                return $diff->s . ' sec';
            }

            return 'now';
        }
    }
@endphp


<div class="app-container container-fluid d-flex align-items-stretch justify-content-between" id="kt_app_header_container">
    <!--begin::Sidebar mobile toggle-->
    <div
      class="d-flex align-items-center d-lg-none ms-n3 me-1 me-md-2"
      title="Show sidebar menu"
    >
      <div
        class="btn btn-icon btn-active-color-primary w-35px h-35px"
        id="kt_app_sidebar_mobile_toggle"
      >
        <i class="ki-duotone ki-abstract-14 fs-2 fs-md-1">
          <span class="path1"></span>
          <span class="path2"></span>
        </i>
      </div>
    </div>
    <!--end::Sidebar mobile toggle-->
    <!--begin::Mobile logo-->
    <div class="d-flex align-items-center flex-grow-1 flex-lg-grow-0">
      <a href="index.html" class="d-lg-none">
        <img
          alt="Logo"
          src="{{ asset('assets/media/logos/default-small.svg') }}"
          class="h-30px"
        />
      </a>
    </div>
    <!--end::Mobile logo-->
    <!--begin::Header wrapper-->
    <div
      class="d-flex align-items-stretch justify-content-between flex-lg-grow-1"
      id="kt_app_header_wrapper"
    >
      <!--begin::Menu wrapper-->
      <div
        class="app-header-menu app-header-mobile-drawer align-items-stretch"
        data-kt-drawer="true"
        data-kt-drawer-name="app-header-menu"
        data-kt-drawer-activate="{default: true, lg: false}"
        data-kt-drawer-overlay="true"
        data-kt-drawer-width="250px"
        data-kt-drawer-direction="end"
        data-kt-drawer-toggle="#kt_app_header_menu_toggle"
        data-kt-swapper="true"
        data-kt-swapper-mode="{default: 'append', lg: 'prepend'}"
        data-kt-swapper-parent="{default: '#kt_app_body', lg: '#kt_app_header_wrapper'}"
      >
        <!--begin::Menu-->
        <div
          class="menu menu-rounded menu-column menu-lg-row my-5 my-lg-0 align-items-stretch fw-semibold px-2 px-lg-0"
          id="kt_app_header_menu"
          data-kt-menu="true"
        >
          <!--begin:Menu item-->
          <div
            data-kt-menu-trigger="{default: 'click', lg: 'hover'}"
            data-kt-menu-placement="bottom-start"
            class="menu-item menu-here-bg menu-lg-down-accordion me-0 me-lg-2 dashboard-navbar"
          >
            <!--begin:Menu link-->
            <a href="{{ route('admin.thongtinchung.index') }}" class="menu-link">
              <span class="menu-title">Dashboards</span>
              <span class="menu-arrow d-lg-none"></span>
            </a>
            <!--end:Menu link-->
          </div>
          <!--end:Menu item-->
          <!--begin:Menu item-->
          <div
            data-kt-menu-trigger="{default: 'click', lg: 'hover'}"
            data-kt-menu-placement="bottom-start"
            class="menu-item menu-lg-down-accordion menu-sub-lg-down-indention me-0 me-lg-2 quanly-navbar"
          >
            <!--begin:Menu link-->
            <span class="menu-link">
              <span class="menu-title">Quản lý</span>
              <span class="menu-arrow d-lg-none"></span>
            </span>
            <!--end:Menu link-->
            <!--begin:Menu sub-->
            <div
              class="menu-sub menu-sub-lg-down-accordion menu-sub-lg-dropdown px-lg-2 py-lg-4 w-lg-250px"
            >
              <!--begin:Menu item-->
              <div
                data-kt-menu-trigger="{default:'click', lg: 'hover'}"
                data-kt-menu-placement="right-start"
                class="menu-item menu-lg-down-accordion quanlydonvi-navbar"
              >
                <!--begin:Menu link-->
                <a href="" class="menu-link">
                  <span class="menu-icon">
                    <i class="ki-duotone ki-bank fs-2">
                      <span class="path1"></span>
                      <span class="path2"></span>
                    </i>
                  </span>
                  <span class="menu-title">Quản lý đơn vị</span>
                </a>
                <!--end:Menu link-->
              </div>
              <!--end:Menu item-->
              <!--begin:Menu item-->
              <div
                data-kt-menu-trigger="{default:'click', lg: 'hover'}"
                data-kt-menu-placement="right-start"
                class="menu-item menu-lg-down-accordion quanlynhansu-navbar"
              >
                <!--begin:Menu link-->
                <a href="" class="menu-link">
                  <span class="menu-icon">
                    <i class="ki-duotone ki-address-book fs-2">
                      <span class="path1"></span>
                      <span class="path2"></span>
                      <span class="path3"></span>
                    </i>
                  </span>
                  <span class="menu-title">Quản lý nhân sự</span>
                </a>
                <!--end:Menu link-->
              </div>
              <!--end:Menu item-->
              <!--begin:Menu item-->
              <div
                data-kt-menu-trigger="{default:'click', lg: 'hover'}"
                data-kt-menu-placement="right-start"
                class="menu-item menu-lg-down-accordion quanlybtc-navbar"
              >
                <!--begin:Menu link-->
                <a href="" class="menu-link">
                  <span class="menu-icon">
                    <i class="ki-duotone ki-element-7 fs-2">
                      <span class="path1"></span>
                      <span class="path2"></span>
                    </i>
                  </span>
                  <span class="menu-title">Quản lý bộ tiêu chuẩn</span>
                </a>
                <!--end:Menu link-->
              </div>
              <!--end:Menu item-->
              <!--begin:Menu item-->
              <div
                data-kt-menu-trigger="{default:'click', lg: 'hover'}"
                data-kt-menu-placement="right-start"
                class="menu-item menu-lg-down-accordion quanlyphanquyen-navbar"
              >
                <!--begin:Menu link-->
                <a href="" class="menu-link">
                  <span class="menu-icon">
                    <i class="ki-duotone ki-abstract-28 fs-2">
                      <span class="path1"></span>
                      <span class="path2"></span>
                    </i>
                  </span>
                  <span class="menu-title">Phân quyền</span>
                </a>
                <!--end:Menu link-->
              </div>
            </div>
            <!--end:Menu sub-->
          </div>
          <!--end:Menu item-->
        </div>
        <!--end::Menu-->
      </div>
      <!--end::Menu wrapper-->
      <!--begin::Navbar-->
      <div class="app-navbar flex-shrink-0">
        <!--begin::Notifications-->
        <div class="app-navbar-item ms-1 ms-md-4" >
          <!--begin::Menu- wrapper-->
          <div
            class="btn btn-icon btn-custom btn-icon-muted btn-active-light btn-active-color-primary w-35px h-35px"
            data-kt-menu-trigger="{default: 'click', lg: 'hover'}"
            data-kt-menu-attach="parent"
            data-kt-menu-placement="bottom-end"
            id="kt_menu_item_wow"
          >
            <i class="ki-duotone ki-notification-status fs-2">
              <span class="path1"></span>
              <span class="path2"></span>
              <span class="path3"></span>
              <span class="path4"></span>
            </i>
          </div>
          <!--begin::Menu-->
          <div
            class="menu menu-sub menu-sub-dropdown menu-column w-350px w-lg-375px"
            data-kt-menu="true"
            id="kt_menu_notifications"
          >
            <!--begin::Heading-->
            <div
              class="d-flex flex-column bgi-no-repeat rounded-top custombg"

            >
              <!--begin::Title-->
              <h3 class="text-white fw-semibold px-9 mt-10 mb-6">
                Thông báo
                {{-- <span class="fs-8 opacity-75 ps-3">24 news</span> --}}
              </h3>
              <!--end::Title-->
              <!--begin::Tabs-->
              <ul
                class="nav nav-line-tabs nav-line-tabs-2x nav-stretch fw-semibold px-9"
              >
                <li class="nav-item">
                  <a
                    class="nav-link text-white opacity-75 opacity-state-100 pb-4"
                    data-bs-toggle="tab"
                    href="#kt_topbar_notifications_1"
                  >
                    Hệ thống
                  </a>
                </li>
              </ul>
              <!--end::Tabs-->
            </div>
            <!--end::Heading-->
            <!--begin::Tab content-->
            <div class="tab-content">
              <!--begin::Tab panel-->
              <div
                class="tab-pane fade show active"
                id="kt_topbar_notifications_1"
                role="tabpanel"
              >
                <!--begin::Items-->
                <div class="scroll-y mh-325px my-5 px-8">

                  <!--begin::Item Info-->
                  {{-- <div class="d-flex flex-stack py-4">
                    <div class="d-flex align-items-center">
                      <div class="symbol symbol-35px me-4">
                        <span class="symbol-label bg-light-primary">
                          <i class="ki-duotone ki-abstract-28 fs-2 text-primary">
                            <span class="path1"></span>
                            <span class="path2"></span>
                          </i>
                        </span>
                      </div>
                      <div class="mb-0 me-2">
                        <a href="#" class="fs-6 text-gray-800 text-hover-primary fw-bold">Project Alice</a>
                        <div class="text-gray-500 fs-7">
                          Phase 1 development
                        </div>
                      </div>
                    </div>
                    <span class="badge badge-light fs-8">1 hr</span>
                  </div> --}}
                  <!--end::Item-->

                  @if(count($notifications) > 0)
                    @foreach($notifications as $noti)
                        @php
                            $userSend = Sentinel::findById($noti->userSend);
                            $notiTime = Carbon::parse($noti->create_at);
                            $timeDiff = customDiffTime($notiTime);
                        @endphp
                        @switch($noti->type)
                            @case('success')
                                <!--begin::Item Success-->
                                <div class="d-flex flex-stack py-4">
                                    <div class="d-flex align-items-center">
                                    <div class="symbol symbol-35px me-4">
                                        <span class="symbol-label bg-light-success">
                                        <i class="ki-duotone ki-abstract-28 fs-2 text-success">
                                            <i class="fa-solid fa-bullhorn text-success"></i>
                                        </i>
                                        </span>
                                    </div>
                                    <div class="mb-0 me-2">
                                        <a href="{{ $noti->url }}" class="fs-6 text-gray-800 text-hover-primary fw-bold">{{ $noti->message }}</a>
                                        <div class="text-gray-500 fs-7">
                                            {{ $userSend->name }} -
                                            {{ DB::table('donvi')->where('id', $userSend->donvi_id)
                                                ->select('ten_donvi')->value('ten_donvi') ?? 'không có đơn vị' }}
                                        </div>
                                    </div>
                                    </div>
                                    <span class="badge badge-light fs-8">{{ $timeDiff }}</span>
                                </div>
                                <!--end::Item-->
                                @break
                            @case('warning')
                                <!--begin::Item Warning-->
                                <div class="d-flex flex-stack py-4">
                                    <div class="d-flex align-items-center">
                                    <div class="symbol symbol-35px me-4">
                                        <span class="symbol-label bg-light-warning">
                                        <i class="ki-duotone ki-abstract-28 fs-2 text-warning">
                                            <i class="fa-solid fa-marker text-warning"></i>
                                        </i>
                                        </span>
                                    </div>
                                    <div class="mb-0 me-2">
                                        <a href="{{ $noti->url }}" class="fs-6 text-gray-800 text-hover-primary fw-bold">{{ $noti->message }}</a>
                                        <div class="text-gray-500 fs-7">
                                            {{ $userSend->name }} -
                                            {{ DB::table('donvi')->where('id', $userSend->donvi_id)
                                                ->select('ten_donvi')->value('ten_donvi') ?? 'không có đơn vị' }}
                                        </div>
                                    </div>
                                    </div>
                                    <span class="badge badge-light fs-8">{{ $timeDiff }}</span>
                                </div>
                                <!--end::Item-->
                                @break
                            @case('danger')
                                <!--begin::Item Danger-->
                                <div class="d-flex flex-stack py-4">
                                    <div class="d-flex align-items-center">
                                        <div class="symbol symbol-35px me-4">
                                            <span class="symbol-label bg-light-danger">
                                            <i class="ki-duotone ki-abstract-28 fs-2 text-danger">
                                                <i class="fa-solid fa-eraser text-danger"></i>
                                            </i>
                                            </span>
                                        </div>
                                        <div class="mb-0 me-2">
                                            <a href="{{ $noti->url }}" class="fs-6 text-gray-800 text-hover-primary fw-bold">{{ $noti->message }}</a>
                                            <div class="text-gray-500 fs-7">
                                                {{ $userSend->name }} -
                                                {{ DB::table('donvi')->where('id', $userSend->donvi_id)
                                                    ->select('ten_donvi')->value('ten_donvi') ?? 'không có đơn vị' }}
                                            </div>
                                        </div>
                                    </div>
                                    <span class="badge badge-light fs-8">{{ $timeDiff }}</span>
                                </div>
                                <!--end::Item-->
                                @break
                        @endswitch
                    @endforeach
                  @endif

                </div>
                <!--end::Items-->
                <!--begin::View more-->
                <div class="py-3 text-center border-top">
                  <a href="" class="btn btn-color-gray-600 btn-active-color-primary">
                    View All
                    <i class="ki-duotone ki-arrow-right fs-5">
                      <span class="path1"></span>
                      <span class="path2"></span> </i
                  ></a>
                </div>
                <!--end::View more-->
              </div>
            </div>
            <!--end::Tab content-->
          </div>
          <!--end::Menu-->
          <!--end::Menu wrapper-->
        </div>
        <!--end::Notifications-->
        <!--begin::Chat-->
        <div class="app-navbar-item ms-1 ms-md-4" style="display:none">
          <!--begin::Menu wrapper-->
          <div
            class="btn btn-icon btn-custom btn-icon-muted btn-active-light btn-active-color-primary w-35px h-35px position-relative"
            id="kt_drawer_chat_toggle"
          >
            <i class="ki-duotone ki-message-text-2 fs-2">
              <span class="path1"></span>
              <span class="path2"></span>
              <span class="path3"></span>
            </i>
            <span
              class="bullet bullet-dot bg-success h-6px w-6px position-absolute translate-middle top-0 start-50 animation-blink"
            ></span>
          </div>
          <!--end::Menu wrapper-->
        </div>
        <!--end::Chat-->

        <!--begin::Theme mode-->
        <div class="app-navbar-item ms-1 ms-md-4">
          <!--begin::Menu toggle-->
          <a
            href="#"
            class="btn btn-icon btn-custom btn-icon-muted btn-active-light btn-active-color-primary w-35px h-35px"
            data-kt-menu-trigger="{default:'click', lg: 'hover'}"
            data-kt-menu-attach="parent"
            data-kt-menu-placement="bottom-end"
          >
            <i class="ki-duotone ki-night-day theme-light-show fs-1">
              <span class="path1"></span>
              <span class="path2"></span>
              <span class="path3"></span>
              <span class="path4"></span>
              <span class="path5"></span>
              <span class="path6"></span>
              <span class="path7"></span>
              <span class="path8"></span>
              <span class="path9"></span>
              <span class="path10"></span>
            </i>
            <i class="ki-duotone ki-moon theme-dark-show fs-1">
              <span class="path1"></span>
              <span class="path2"></span>
            </i>
          </a>
          <!--begin::Menu toggle-->
          <!--begin::Menu-->
          <div
            class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-title-gray-700 menu-icon-gray-500 menu-active-bg menu-state-color fw-semibold py-4 fs-base w-150px"
            data-kt-menu="true"
            data-kt-element="theme-mode-menu"
          >
            <!--begin::Menu item-->
            <div class="menu-item px-3 my-0">
              <a
                href="#"
                class="menu-link px-3 py-2"
                data-kt-element="mode"
                data-kt-value="light"
              >
                <span class="menu-icon" data-kt-element="icon">
                  <i class="ki-duotone ki-night-day fs-2">
                    <span class="path1"></span>
                    <span class="path2"></span>
                    <span class="path3"></span>
                    <span class="path4"></span>
                    <span class="path5"></span>
                    <span class="path6"></span>
                    <span class="path7"></span>
                    <span class="path8"></span>
                    <span class="path9"></span>
                    <span class="path10"></span>
                  </i>
                </span>
                <span class="menu-title">Light</span>
              </a>
            </div>
            <!--end::Menu item-->
            <!--begin::Menu item-->
            <div class="menu-item px-3 my-0">
              <a
                href="#"
                class="menu-link px-3 py-2"
                data-kt-element="mode"
                data-kt-value="dark"
              >
                <span class="menu-icon" data-kt-element="icon">
                  <i class="ki-duotone ki-moon fs-2">
                    <span class="path1"></span>
                    <span class="path2"></span>
                  </i>
                </span>
                <span class="menu-title">Dark</span>
              </a>
            </div>
            <!--end::Menu item-->
            <!--begin::Menu item-->
            <div class="menu-item px-3 my-0">
              <span id="btn-light-sidebar" class="menu-link px-3 py-2">
                <span class="menu-icon" data-kt-element="icon">
                  <i class="ki-duotone ki-night-day fs-2">
                    <span class="path1"></span>
                    <span class="path2"></span>
                    <span class="path3"></span>
                    <span class="path4"></span>
                    <span class="path5"></span>
                    <span class="path6"></span>
                    <span class="path7"></span>
                    <span class="path8"></span>
                    <span class="path9"></span>
                    <span class="path10"></span>
                  </i>
                </span>
                <span class="menu-title">Light Sidebar</span>
              </span>
            </div>
            <!--end::Menu item-->
            <!--begin::Menu item-->
            <div class="menu-item px-3 my-0">
              <span class="menu-link px-3 py-2" id="btn-dark-sidebar">
                <span class="menu-icon" data-kt-element="icon">
                  <i class="ki-duotone ki-moon fs-2">
                    <span class="path1"></span>
                    <span class="path2"></span>
                  </i>
                </span>
                <span class="menu-title">Dark Sidebar</span>
              </span>
            </div>
            <!--end::Menu item-->
          </div>
          <!--end::Menu-->
        </div>
        <!--end::Theme mode-->
        <!--begin::User menu-->
        <div
          class="app-navbar-item ms-1 ms-md-4"
          id="kt_header_user_menu_toggle"
        >
          <!--begin::Menu wrapper-->
          <div
            class="cursor-pointer symbol symbol-35px"
            data-kt-menu-trigger="{default: 'click', lg: 'hover'}"
            data-kt-menu-attach="parent"
            data-kt-menu-placement="bottom-end"
          >
            <img
                @if ($user->image == null)
                    @if ($user->gender == "Nam")
                        src="{{ asset('userinfo/default/male.jpg') }}"
                    @elseif  ($user->gender == "Nữ")
                        src="{{ asset('userinfo/default/female.jpg') }}"
                    @else
                        src="{{ asset('userinfo/default/male.jpg') }}"
                    @endif
                @else
                    src="{{ asset($user->image) }}"
                @endif
              class="rounded-3"
              alt="user"
            />
          </div>
          <!--begin::User account menu-->
          <div
            class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-800 menu-state-bg menu-state-color fw-semibold py-4 fs-6 w-275px"
            data-kt-menu="true"
          >
            <!--begin::Menu item-->
            <div class="menu-item px-3">
              <div class="menu-content d-flex align-items-center px-3">
                <!--begin::Avatar-->
                <!--end::Avatar-->
                <!--begin::Username-->
                <div class="d-flex flex-column">
                  <div class="fw-bold d-flex align-items-center fs-5">
                    {{ $user->name; }}
                  </div>
                  <a
                    href="#"
                    class="fw-semibold text-muted text-hover-primary fs-7"
                    >{{ $user->email; }}</a
                  >
                </div>
                <!--end::Username-->
              </div>
            </div>
            <div class="separator my-2"></div>
            <div class="menu-item px-5">
              <a href="" class="menu-link px-5">My Profile</a>
            </div>
            {{-- Ngôn ngữ --}}
            {{-- <div class="menu-item px-5" data-kt-menu-trigger="{default: 'click', lg: 'hover'}" data-kt-menu-placement="left-start" data-kt-menu-offset="-15px, 0">
              <a href="#" class="menu-link px-5">
                <span class="menu-title position-relative">Ngôn ngữ
                  <span class="fs-8 rounded bg-light px-3 py-2 position-absolute translate-middle-y top-50 end-0">
                    Việt Nam
                    <img class="w-15px h-15px rounded-1 ms-2" src="{{ asset('assets/media/flags/vietnam.svg') }}"alt="" />
                  </span>
                </span>
              </a>
              <div class="menu-sub menu-sub-dropdown w-175px py-4">
                <div class="menu-item px-3">
                  <a href="#" class="menu-link d-flex px-5 active">
                    <span class="symbol symbol-20px me-4">
                      <img class="rounded-1" src="{{ asset('assets/media/flags/vietnam.svg') }}" alt=""/>
                    </span>
                    Việt Nam
                    </a>
                </div>
                <div class="menu-item px-3">
                    <a href="#" class="menu-link d-flex px-5">
                        <span class="symbol symbol-20px me-4">
                        <img class="rounded-1" src="{{ asset('assets/media/flags/united-states.svg') }}" alt="" />
                        </span>
                        English
                    </a>
                </div>
              </div>
            </div> --}}
            <!--end::Menu item-->
            <!--begin::Menu item-->
            <div class="menu-item px-5">
              <a
                href="{!! route('admin.logout') !!}"
                class="menu-link px-5"
                >Sign Out</a
              >
            </div>
            <!--end::Menu item-->
          </div>
          <!--end::User account menu-->
          <!--end::Menu wrapper-->
        </div>
        <!--end::User menu-->
        <!--begin::Header menu toggle-->
        <div
          class="app-navbar-item d-lg-none ms-2 me-n2"
          title="Show header menu"
        >
          <div
            class="btn btn-flex btn-icon btn-active-color-primary w-30px h-30px"
            id="kt_app_header_menu_toggle"
          >
            <i class="ki-duotone ki-element-4 fs-1">
              <span class="path1"></span>
              <span class="path2"></span>
            </i>
          </div>
        </div>
        <!--end::Header menu toggle-->
        <!--begin::Aside toggle-->
        <!--end::Header menu toggle-->
      </div>
      <!--end::Navbar-->
    </div>
    <!--end::Header wrapper-->
  </div>
  <!--end::Header container-->
</div>
