<footer class="bg-white antialiased dark:bg-gray-800">
  <div class="mx-auto max-w-screen-xl px-4 2xl:px-0">
    <div class="border-b border-gray-100 py-6 dark:border-gray-700 md:py-8 lg:py-16">
      <div class="items-start gap-6 md:gap-8 lg:flex 2xl:gap-24">
        <div class="grid min-w-0 flex-1 grid-cols-2 gap-6 md:gap-8 xl:grid-cols-3">
          <div>
            <h6 class="mb-4 text-md font-bold text-gray-900 dark:text-white">Server</h6>
            <ul class="space-y-3">
              <li>
                <a href="{{ route('rules') }}" class="text-gray-500 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white">
                    Rules & Guidelines
                </a>
              </li>

              <li>
                <a href="{{ route('ranks') }}" class="text-gray-500 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white">
                    Ranks
                </a>
              </li>

              <li>
                <a href="{{ route('staff') }}" class="text-gray-500 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white">
                    List of Staff
                </a>
              </li>

              <li>
                <a href="{{ route('front.maps') }}" class="text-gray-500 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white">
                    Live Maps
                </a>
              </li>
            </ul>
          </div>

          <div>
            <h6 class="mb-4 text-md font-bold text-gray-900 dark:text-white">Community</h6>
            <ul class="space-y-3">
              <li>
                <a href="{{ route('portal') }}" class="text-gray-500 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white">
                    Portal
                </a>
              </li>
              <li>
                <a href="{{ route('vote') }}" class="text-gray-500 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white">
                    Vote For Our Server
                </a>
              </li>
              <li>
                <a href="{{ route('front.donate') }}" class="text-gray-500 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white">
                    Donate
                </a>
              </li>
              <li>
                <a href="{{ route('front.banlist') }}" class="text-gray-500 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white">
                    Banned Players
                </a>
              </li>
            </ul>
          </div>

          <div>
            <h6 class="mb-4 text-md font-bold text-gray-900 dark:text-white">Forms</h6>
            <ul class="space-y-3">
              <li>
                <a href="{{ route('report') }}" class="text-gray-500 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white">
                    Report a Player
                </a>
              </li>
              <li>
                <a href="{{ route('front.appeal') }}" class="text-gray-500 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white">
                    Appeal a Ban
                </a>
              </li>
              <li>
                <a href="{{ route('front.rank-up') }}" class="text-gray-500 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white">
                    Apply for Build Rank
                </a>
              </li>
              <li>
                <a href="{{ route('staff-apply') }}" class="text-gray-500 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white">
                    Apply for Staff
                </a>
              </li>
              <li>
                <a href="{{ route('front.contact') }}" class="text-gray-500 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white">
                    Contact Us
                </a>
              </li>
            </ul>
          </div>

          <div>
            <h6 class="mb-4 text-md font-bold text-gray-900 dark:text-white">Archives</h6>
            <ul class="space-y-3">
                <li>
                <a href="{{ route('wiki') }}" class="text-gray-500 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white">
                    Map Archive
                </a>
              </li>
              <li>
                <a href="https://forums.projectcitybuild.com" class="text-gray-500 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white">
                    Community Forums
                </a>
              </li>
              <li>
                <a href="{{ route('wiki') }}" class="text-gray-500 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white">
                    Community Wiki
                </a>
              </li>
            </ul>
          </div>
        </div>

        <div class="mt-6 w-full md:mt-8 lg:mt-0 lg:max-w-lg">
            <x-donation-card />
        </div>
      </div>
    </div>

    <div class="py-6 md:py-8">
      <div class="gap-4 space-y-5 xl:flex xl:items-center xl:justify-between xl:space-y-0">
        <ul class="flex flex-wrap items-center gap-4 text-sm text-gray-900 dark:text-white xl:justify-center">
          <li><a href="{{ route('terms') }}" title="" class="font-medium hover:underline">Terms of Use</a></li>
          <li><a href="{{ route('privacy') }}" title="" class="font-medium hover:underline">Privacy Policy</a></li>
        </ul>

        <div class="flex space-x-4">
            <a href="https://www.facebook.com/ProjectCityBuild" target="_blank" class="text-gray-500 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white">
                <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path fill-rule="evenodd" d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z" clip-rule="evenodd" />
                </svg>
            </a>

            <a href="https://www.instagram.com/projectcitybuild" target="_blank" class="text-gray-500 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white">
                <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path
                    fill-rule="evenodd"
                    d="M12.315 2c2.43 0 2.784.013 3.808.06 1.064.049 1.791.218 2.427.465a4.902 4.902 0 011.772 1.153 4.902 4.902 0 011.153 1.772c.247.636.416 1.363.465 2.427.048 1.067.06 1.407.06 4.123v.08c0 2.643-.012 2.987-.06 4.043-.049 1.064-.218 1.791-.465 2.427a4.902 4.902 0 01-1.153 1.772 4.902 4.902 0 01-1.772 1.153c-.636.247-1.363.416-2.427.465-1.067.048-1.407.06-4.123.06h-.08c-2.643 0-2.987-.012-4.043-.06-1.064-.049-1.791-.218-2.427-.465a4.902 4.902 0 01-1.772-1.153 4.902 4.902 0 01-1.153-1.772c-.247-.636-.416-1.363-.465-2.427-.047-1.024-.06-1.379-.06-3.808v-.63c0-2.43.013-2.784.06-3.808.049-1.064.218-1.791.465-2.427a4.902 4.902 0 011.153-1.772A4.902 4.902 0 015.45 2.525c.636-.247 1.363-.416 2.427-.465C8.901 2.013 9.256 2 11.685 2h.63zm-.081 1.802h-.468c-2.456 0-2.784.011-3.807.058-.975.045-1.504.207-1.857.344-.467.182-.8.398-1.15.748-.35.35-.566.683-.748 1.15-.137.353-.3.882-.344 1.857-.047 1.023-.058 1.351-.058 3.807v.468c0 2.456.011 2.784.058 3.807.045.975.207 1.504.344 1.857.182.466.399.8.748 1.15.35.35.683.566 1.15.748.353.137.882.3 1.857.344 1.054.048 1.37.058 4.041.058h.08c2.597 0 2.917-.01 3.96-.058.976-.045 1.505-.207 1.858-.344.466-.182.8-.398 1.15-.748.35-.35.566-.683.748-1.15.137-.353.3-.882.344-1.857.048-1.055.058-1.37.058-4.041v-.08c0-2.597-.01-2.917-.058-3.96-.045-.976-.207-1.505-.344-1.858a3.097 3.097 0 00-.748-1.15 3.098 3.098 0 00-1.15-.748c-.353-.137-.882-.3-1.857-.344-1.023-.047-1.351-.058-3.807-.058zM12 6.865a5.135 5.135 0 110 10.27 5.135 5.135 0 010-10.27zm0 1.802a3.333 3.333 0 100 6.666 3.333 3.333 0 000-6.666zm5.338-3.205a1.2 1.2 0 110 2.4 1.2 1.2 0 010-2.4z"
                    clip-rule="evenodd"
                    />
                </svg>
            </a>

            <a href="https://www.youtube.com/user/PCBMinecraft" target="_blank" class="text-gray-500 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path fill-rule="evenodd" d="M21.7 8.037a4.26 4.26 0 0 0-.789-1.964 2.84 2.84 0 0 0-1.984-.839c-2.767-.2-6.926-.2-6.926-.2s-4.157 0-6.928.2a2.836 2.836 0 0 0-1.983.839 4.225 4.225 0 0 0-.79 1.965 30.146 30.146 0 0 0-.2 3.206v1.5a30.12 30.12 0 0 0 .2 3.206c.094.712.364 1.39.784 1.972.604.536 1.38.837 2.187.848 1.583.151 6.731.2 6.731.2s4.161 0 6.928-.2a2.844 2.844 0 0 0 1.985-.84 4.27 4.27 0 0 0 .787-1.965 30.12 30.12 0 0 0 .2-3.206v-1.516a30.672 30.672 0 0 0-.202-3.206Zm-11.692 6.554v-5.62l5.4 2.819-5.4 2.801Z" clip-rule="evenodd"/>
                </svg>
            </a>

            <a href="{{ config('discord.invite_url') }}" target="_blank" class="text-gray-500 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white">
                <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M18.942 5.556a16.3 16.3 0 0 0-4.126-1.3 12.04 12.04 0 0 0-.529 1.1 15.175 15.175 0 0 0-4.573 0 11.586 11.586 0 0 0-.535-1.1 16.274 16.274 0 0 0-4.129 1.3 17.392 17.392 0 0 0-2.868 11.662 15.785 15.785 0 0 0 4.963 2.521c.41-.564.773-1.16 1.084-1.785a10.638 10.638 0 0 1-1.706-.83c.143-.106.283-.217.418-.331a11.664 11.664 0 0 0 10.118 0c.137.114.277.225.418.331-.544.328-1.116.606-1.71.832a12.58 12.58 0 0 0 1.084 1.785 16.46 16.46 0 0 0 5.064-2.595 17.286 17.286 0 0 0-2.973-11.59ZM8.678 14.813a1.94 1.94 0 0 1-1.8-2.045 1.93 1.93 0 0 1 1.8-2.047 1.918 1.918 0 0 1 1.8 2.047 1.929 1.929 0 0 1-1.8 2.045Zm6.644 0a1.94 1.94 0 0 1-1.8-2.045 1.93 1.93 0 0 1 1.8-2.047 1.919 1.919 0 0 1 1.8 2.047 1.93 1.93 0 0 1-1.8 2.045Z"/>
                </svg>
            </a>

            <a href="https://github.com/projectcitybuild" target="_blank" class="text-gray-500 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white">
                <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path
                    fill-rule="evenodd"
                    d="M12 2C6.477 2 2 6.484 2 12.017c0 4.425 2.865 8.18 6.839 9.504.5.092.682-.217.682-.483 0-.237-.008-.868-.013-1.703-2.782.605-3.369-1.343-3.369-1.343-.454-1.158-1.11-1.466-1.11-1.466-.908-.62.069-.608.069-.608 1.003.07 1.531 1.032 1.531 1.032.892 1.53 2.341 1.088 2.91.832.092-.647.35-1.088.636-1.338-2.22-.253-4.555-1.113-4.555-4.951 0-1.093.39-1.988 1.029-2.688-.103-.253-.446-1.272.098-2.65 0 0 .84-.27 2.75 1.026A9.564 9.564 0 0112 6.844c.85.004 1.705.115 2.504.337 1.909-1.296 2.747-1.027 2.747-1.027.546 1.379.202 2.398.1 2.651.64.7 1.028 1.595 1.028 2.688 0 3.848-2.339 4.695-4.566 4.943.359.309.678.92.678 1.855 0 1.338-.012 2.419-.012 2.747 0 .268.18.58.688.482A10.019 10.019 0 0022 12.017C22 6.484 17.522 2 12 2z"
                    clip-rule="evenodd"
                    />
                </svg>
            </a>
        </div>

        <div class="text-sm text-gray-600 dark:text-gray-400 text-right">
            <h6>© Project City Build {{ date('Y') }}</h6>

            <p class="mt-1 text-gray-400">
                Not affiliated with Mojang or Microsoft
            </p>
        </div>
      </div>
    </div>
  </div>
</footer>
