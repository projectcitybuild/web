import type { SidebarSection } from '../../Navigation/Sidebar'

export function filterSidebar(
  menu: SidebarSection[],
  can: (permission: App.Domains.Permissions.WebManagePermission | App.Domains.Permissions.WebReviewPermission) => boolean
): SidebarSection[] {

  return menu
    .map(section => {
      const visibleChildren = section.children.filter(
        child => !child.permission || can(child.permission),
      )
      return {
        ...section,
        children: visibleChildren
      }
    })
    .filter(section => section.children.length > 0)
}
