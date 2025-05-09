
import React, { useState } from "react";
import ContentForm from "@/components/ContentForm";
import ContentCarousel from "@/components/ContentCarousel";
import ContentManager from "@/components/ContentManager";
import { Tabs, TabsContent, TabsList, TabsTrigger } from "@/components/ui/tabs";
import { useContent } from "@/context/ContentContext";
import { ContentProvider } from "@/context/ContentContext";

const Index = () => {
  const [activeTab, setActiveTab] = useState<string>("submit");
  
  return (
    <ContentProvider>
      <div className="container px-4 py-8 mx-auto max-w-6xl">
        <header className="mb-8 text-center">
          <h1 className="text-3xl font-bold mb-2">Carousel Content Curator</h1>
          <p className="text-muted-foreground">Upload and manage content for your carousel</p>
        </header>
        
        <Tabs defaultValue="submit" value={activeTab} onValueChange={setActiveTab}>
          <div className="flex justify-center mb-6">
            <TabsList>
              <TabsTrigger value="submit">Submit Content</TabsTrigger>
              <TabsTrigger value="manage">Manage Content</TabsTrigger>
            </TabsList>
          </div>
          
          <TabsContent value="submit" className="space-y-8">
            <ContentForm />
            <PreviewSection />
          </TabsContent>
          
          <TabsContent value="manage">
            <ContentManager />
          </TabsContent>
        </Tabs>
      </div>
    </ContentProvider>
  );
};

const PreviewSection = () => {
  const { contents } = useContent();
  
  return (
    <div className="mt-8">
      <h2 className="text-2xl font-bold text-center mb-6">Preview Carousel</h2>
      <ContentCarousel items={contents} />
      <p className="text-sm text-muted-foreground text-center mt-4">
        This preview shows how your content will appear in the carousel.
      </p>
    </div>
  );
};

export default Index;
