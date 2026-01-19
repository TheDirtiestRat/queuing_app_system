<div class="drawer-side is-drawer-close:overflow-visible p-2">
    <label for="my-drawer-4" aria-label="close sidebar" class="drawer-overlay"></label>
    <div
        class="flex min-h-full flex-col items-start bg-base-300 rounded-2xl shadow is-drawer-close:w-14 is-drawer-open:w-64 ">
        <!-- Sidebar content here -->
        <ul class="menu w-full gap-1 grow">
            <!-- List item -->
            <li>
                <a href="{{ url('/admin/menu') }}">
                    <button class="is-drawer-close:tooltip is-drawer-close:tooltip-right flex items-center gap-3 h-8" data-tip="Menu">
                        <!-- Home icon -->
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-linejoin="round"
                            stroke-linecap="round" stroke-width="2" fill="none" stroke="currentColor"
                            class="my-1.5 inline-block size-4">
                            <path d="M15 21v-8a1 1 0 0 0-1-1h-4a1 1 0 0 0-1 1v8"></path>
                            <path
                                d="M3 10a2 2 0 0 1 .709-1.528l7-5.999a2 2 0 0 1 2.582 0l7 5.999A2 2 0 0 1 21 10v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z">
                            </path>
                        </svg>
                        <span class="is-drawer-close:hidden">Menu</span>
                    </button>
                </a>
            </li>

            <!-- List item -->
            <li>
                <a href="{{ url('/display-monitor') }}" target="_blank">
                    <button class="is-drawer-close:tooltip is-drawer-close:tooltip-right flex items-center gap-3 h-8" data-tip="Monitor">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                            class="icon icon-tabler icons-tabler-outline icon-tabler-user-code size-4">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path d="M8 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0" />
                            <path d="M6 21v-2a4 4 0 0 1 4 -4h3.5" />
                            <path d="M20 21l2 -2l-2 -2" />
                            <path d="M17 17l-2 2l2 2" />
                        </svg>
                        <span class="is-drawer-close:hidden">Monitor</span>
                    </button>
                </a>
            </li>

            {{-- <!-- List item -->
            <li>
                <a href="{{ url('/admin/challenges') }}">
                    <button class="is-drawer-close:tooltip is-drawer-close:tooltip-right flex items-center gap-3 h-8" data-tip="Challenges">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                            class="icon icon-tabler icons-tabler-outline icon-tabler-swords size-4">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path d="M21 3v5l-11 9l-4 4l-3 -3l4 -4l9 -11l5 0" />
                            <path d="M5 13l6 6" />
                            <path d="M14.32 17.32l3.68 3.68l3 -3l-3.365 -3.365" />
                            <path d="M10 5.5l-2 -2.5h-5v5l3 2.5" />
                        </svg>
                        <span class="is-drawer-close:hidden">Code Problems</span>
                    </button>
                </a>
            </li>

            <!-- List item -->
            <li class="basis-auto">
                <a href="{{ url('admin/options') }}">
                    <button class="is-drawer-close:tooltip is-drawer-close:tooltip-right flex items-center gap-3 h-8" data-tip="Options">
                        <!-- Settings icon -->
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                            class="icon icon-tabler icons-tabler-outline icon-tabler-adjustments-horizontal size-4">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path d="M12 6a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" />
                            <path d="M4 6l8 0" />
                            <path d="M16 6l4 0" />
                            <path d="M6 12a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" />
                            <path d="M4 12l2 0" />
                            <path d="M10 12l10 0" />
                            <path d="M15 18a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" />
                            <path d="M4 18l11 0" />
                            <path d="M19 18l1 0" />
                        </svg>
                        <span class="is-drawer-close:hidden">Options</span>
                    </button>
                </a>
            </li> --}}
        </ul>
    </div>
</div>
